<?php
/**
 * ShortLink Manager - QR Code Generator
 *
 * Wraps the chillerlan/php-qrcode library to produce branded QR codes
 * with custom finder-pattern colours and optional centred logo.
 *
 * @package shortlinkmgr
 */

require_once __DIR__ . '/vendor/autoload.php';

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Data\QRMatrix;

class ShortlinkmgrQRGenerator
{
    /** @var modX */
    protected $modx;

    /** @var int QR image dimension in pixels (square) */
    protected $size = 800;

    /** @var string|null Hex background colour, null = transparent */
    protected $bgColor = '#FFFFFF';

    /** @var string Hex colour for data/timing/alignment/format/version modules */
    protected $patternColor = '#000000';

    /** @var string Hex colour for the outer border of the three finder patterns */
    protected $finderBorderColor = '#000000';

    /** @var string Hex colour for the 3×3 centre eye of the finder patterns */
    protected $finderEyeColor = '#000000';

    /** @var string Relative server path to a square SVG logo file (or empty) */
    protected $logoFile = '';

    /** @var string Filename prefix for generated files */
    protected $prefix = 'qrcode';

    /** @var string Absolute path to the qr-codes output directory */
    protected $outputDir;

    /** @var string Web-accessible URL to the qr-codes output directory */
    protected $outputUrl;

    /**
     * @param modX $modx
     */
    public function __construct($modx)
    {
        $this->modx = $modx;
        $this->loadSettings();
    }

    /**
     * Read QR-related system settings from MODX.
     */
    protected function loadSettings()
    {
        $assetsPath = $this->modx->getOption(
            'shortlinkmgr.assets_path',
            null,
            $this->modx->getOption('assets_path') . 'components/shortlinkmgr/'
        );
        $assetsUrl = $this->modx->getOption(
            'shortlinkmgr.assets_url',
            null,
            $this->modx->getOption('assets_url') . 'components/shortlinkmgr/'
        );

        $this->outputDir = rtrim($assetsPath, '/') . '/qr-codes/';
        $this->outputUrl = rtrim($assetsUrl, '/') . '/qr-codes/';

        $this->prefix           = $this->modx->getOption('shortlinkmgr.qr_prefix', null, 'qrcode');
        $this->size             = (int) $this->modx->getOption('shortlinkmgr.qr_size', null, 800);
        $this->bgColor          = $this->modx->getOption('shortlinkmgr.qr_bg_color', null, '#FFFFFF');
        $this->patternColor     = $this->modx->getOption('shortlinkmgr.qr_pattern_color', null, '#000000');
        $this->finderBorderColor = $this->modx->getOption('shortlinkmgr.qr_finder_border_color', null, '#000000');
        $this->finderEyeColor   = $this->modx->getOption('shortlinkmgr.qr_finder_eye_color', null, '#000000');
        $this->logoFile         = $this->modx->getOption('shortlinkmgr.qr_logo_file', null, '');

        // Enforce fallback to black for empty colour values
        if (empty($this->patternColor))      $this->patternColor      = '#000000';
        if (empty($this->finderBorderColor)) $this->finderBorderColor = '#000000';
        if (empty($this->finderEyeColor))    $this->finderEyeColor    = '#000000';

        // Empty background = transparent (stored as null internally)
        if (empty($this->bgColor)) {
            $this->bgColor = null;
        }
    }

    // ── Public API ───────────────────────────────────────────────────────────────

    /**
     * Generate SVG and PNG QR code files for a given URL and link ID.
     *
     * @param  string $url    Full shortlink URL to encode
     * @param  int    $linkId ShortlinkMgrLink record ID
     * @return array  {svg_url, png_url, svg_content, short_url}
     */
    public function generate($url, $linkId)
    {
        $this->ensureOutputDir();

        $svgPath = $this->getSvgPath($linkId);
        $pngPath = $this->getPngPath($linkId);

        $svgContent = $this->renderSVG($url);
        file_put_contents($svgPath, $svgContent);

        $pngUrl = '';
        if (extension_loaded('gd')) {
            $pngData = $this->renderPNG($url);
            file_put_contents($pngPath, $pngData);
            $pngUrl = $this->outputUrl . $this->getFilename($linkId, 'png');
        } else {
            $this->modx->log(\modX::LOG_LEVEL_WARN, '[ShortlinkMgr QR] ext-gd not loaded — PNG generation skipped.');
        }

        return [
            'svg_url'     => $this->outputUrl . $this->getFilename($linkId, 'svg'),
            'png_url'     => $pngUrl,
            'svg_content' => $svgContent,
            'short_url'   => $url,
        ];
    }

    /**
     * Check whether QR files already exist for a link.
     */
    public function filesExist($linkId)
    {
        // SVG is the primary output; PNG is optional (requires ext-gd)
        return file_exists($this->getSvgPath($linkId));
    }

    /**
     * Delete existing QR files for a link.
     */
    public function deleteFiles($linkId)
    {
        $svg = $this->getSvgPath($linkId);
        $png = $this->getPngPath($linkId);
        if (file_exists($svg)) @unlink($svg);
        if (file_exists($png)) @unlink($png);
    }

    /**
     * Return URLs to existing files (without regenerating).
     */
    public function getFileUrls($linkId)
    {
        return [
            'svg_url' => $this->outputUrl . $this->getFilename($linkId, 'svg'),
            'png_url' => $this->outputUrl . $this->getFilename($linkId, 'png'),
        ];
    }

    /**
     * Read the SVG content of an already-generated file.
     */
    public function getSvgContent($linkId)
    {
        $path = $this->getSvgPath($linkId);
        return file_exists($path) ? file_get_contents($path) : '';
    }

    // ── SVG Rendering ────────────────────────────────────────────────────────────

    /**
     * Generate a QR code as an SVG string.
     */
    protected function renderSVG($url)
    {
        $hasLogo     = $this->hasLogo();
        $eccLevel    = $hasLogo ? QRCode::ECC_H : QRCode::ECC_M;
        $lightColor  = $this->bgColor ?? 'transparent';

        $options = new QROptions([
            'version'          => QRCode::VERSION_AUTO,
            'eccLevel'         => $eccLevel,
            'outputType'       => QRCode::OUTPUT_MARKUP_SVG,
            'addQuietzone'     => true,
            'quietzoneSize'    => 2,
            'imageBase64'      => false,
            'svgOpacity'       => 1.0,
            'svgDefs'          => '',
            'cssClass'         => '',
            'markupDark'       => $this->patternColor,
            'markupLight'      => $lightColor,
            'moduleValues'     => $this->getSvgModuleValues($lightColor),
        ]);

        $qrcode = new QRCode($options);
        $matrix = $qrcode->getMatrix($url);

        // If a logo is present, clear the centre area
        if ($hasLogo) {
            $logoModules = $this->calculateLogoModules($matrix);
            $matrix->setLogoSpace($logoModules, $logoModules);
        }

        // Render SVG from the matrix
        $outputClass = new \chillerlan\QRCode\Output\QRMarkup($options, $matrix);
        $svg = $outputClass->dump();

        // Set explicit viewBox and dimensions
        $moduleCount = $matrix->size();
        $svg = $this->rewriteSvgDimensions($svg, $moduleCount);

        // Embed the logo if configured
        if ($hasLogo) {
            $svg = $this->embedLogoInSvg($svg, $matrix);
        }

        return $svg;
    }

    // ── PNG Rendering ────────────────────────────────────────────────────────────

    /**
     * Generate a QR code as raw PNG data.
     */
    protected function renderPNG($url)
    {
        $hasLogo  = $this->hasLogo();
        $eccLevel = $hasLogo ? QRCode::ECC_H : QRCode::ECC_M;

        // Calculate scale so that (moduleCount * scale) ≈ desired size
        // We'll use a generous scale first then resize to exact dimensions
        $tempOptions = new QROptions([
            'version'       => QRCode::VERSION_AUTO,
            'eccLevel'      => $eccLevel,
            'outputType'    => QRCode::OUTPUT_MARKUP_SVG,
            'addQuietzone'  => true,
            'quietzoneSize' => 2,
        ]);
        $tempQr = new QRCode($tempOptions);
        $tempMatrix = $tempQr->getMatrix($url);
        $moduleCount = $tempMatrix->size();

        $scale = max(1, (int) ceil($this->size / $moduleCount));

        $isTransparent = ($this->bgColor === null);
        $bgRgb = $this->hexToRgb($this->bgColor ?? '#FFFFFF');

        $options = new QROptions([
            'version'             => QRCode::VERSION_AUTO,
            'eccLevel'            => $eccLevel,
            'outputType'          => QRCode::OUTPUT_IMAGE_PNG,
            'addQuietzone'        => true,
            'quietzoneSize'       => 2,
            'scale'               => $scale,
            'imageBase64'         => false,
            'imageTransparent'    => $isTransparent,
            'imageTransparencyBG' => $bgRgb,
            'pngCompression'      => 9,
            'moduleValues'        => $this->getPngModuleValues($bgRgb),
        ]);

        $qrcode = new QRCode($options);
        $matrix = $qrcode->getMatrix($url);

        if ($hasLogo) {
            $logoModules = $this->calculateLogoModules($matrix);
            $matrix->setLogoSpace($logoModules, $logoModules);
        }

        // Get the GD resource
        $options->returnResource = true;
        $outputClass = new \chillerlan\QRCode\Output\QRImage($options, $matrix);
        $gdImage = $outputClass->dump();

        // Resize to exact desired dimensions
        $rawSize = $moduleCount * $scale;
        if ($rawSize !== $this->size) {
            $resized = imagecreatetruecolor($this->size, $this->size);
            if ($isTransparent) {
                imagesavealpha($resized, true);
                $trans = imagecolorallocatealpha($resized, 0, 0, 0, 127);
                imagefill($resized, 0, 0, $trans);
            }
            imagecopyresampled($resized, $gdImage, 0, 0, 0, 0, $this->size, $this->size, $rawSize, $rawSize);
            imagedestroy($gdImage);
            $gdImage = $resized;
        }

        // Overlay logo onto PNG if configured
        if ($hasLogo) {
            $gdImage = $this->overlayLogoOnPng($gdImage, $matrix, $moduleCount, $scale);
        }

        // Capture PNG output
        ob_start();
        imagepng($gdImage, null, 9);
        $pngData = ob_get_clean();
        imagedestroy($gdImage);

        return $pngData;
    }

    // ── Module Value Maps ────────────────────────────────────────────────────────

    /**
     * Build the moduleValues array for SVG output (CSS colour strings).
     */
    protected function getSvgModuleValues($lightColor)
    {
        $dark    = $this->patternColor;
        $light   = $lightColor;
        $fBorder = $this->finderBorderColor;
        $fEye    = $this->finderEyeColor;

        return [
            // Light modules
            QRMatrix::M_NULL             => $light,
            QRMatrix::M_DARKMODULE_LIGHT => $light,
            QRMatrix::M_DATA             => $light,
            QRMatrix::M_FINDER           => $light,
            QRMatrix::M_SEPARATOR        => $light,
            QRMatrix::M_ALIGNMENT        => $light,
            QRMatrix::M_TIMING           => $light,
            QRMatrix::M_FORMAT           => $light,
            QRMatrix::M_VERSION          => $light,
            QRMatrix::M_QUIETZONE        => $light,
            QRMatrix::M_LOGO             => $light,
            QRMatrix::M_FINDER_DOT_LIGHT => $light,
            // Dark modules
            QRMatrix::M_DARKMODULE       => $dark,
            QRMatrix::M_DATA_DARK        => $dark,
            QRMatrix::M_FINDER_DARK      => $fBorder,
            QRMatrix::M_ALIGNMENT_DARK   => $dark,
            QRMatrix::M_TIMING_DARK      => $dark,
            QRMatrix::M_FORMAT_DARK      => $dark,
            QRMatrix::M_VERSION_DARK     => $dark,
            QRMatrix::M_QUIETZONE_DARK   => $dark,
            QRMatrix::M_LOGO_DARK        => $dark,
            QRMatrix::M_FINDER_DOT       => $fEye,
            QRMatrix::M_SEPARATOR_DARK   => $light,
        ];
    }

    /**
     * Build the moduleValues array for PNG output (RGB arrays).
     */
    protected function getPngModuleValues($bgRgb)
    {
        $dark    = $this->hexToRgb($this->patternColor);
        $light   = $bgRgb;
        $fBorder = $this->hexToRgb($this->finderBorderColor);
        $fEye    = $this->hexToRgb($this->finderEyeColor);

        return [
            QRMatrix::M_NULL             => $light,
            QRMatrix::M_DARKMODULE_LIGHT => $light,
            QRMatrix::M_DATA             => $light,
            QRMatrix::M_FINDER           => $light,
            QRMatrix::M_SEPARATOR        => $light,
            QRMatrix::M_ALIGNMENT        => $light,
            QRMatrix::M_TIMING           => $light,
            QRMatrix::M_FORMAT           => $light,
            QRMatrix::M_VERSION          => $light,
            QRMatrix::M_QUIETZONE        => $light,
            QRMatrix::M_LOGO             => $light,
            QRMatrix::M_FINDER_DOT_LIGHT => $light,
            QRMatrix::M_DARKMODULE       => $dark,
            QRMatrix::M_DATA_DARK        => $dark,
            QRMatrix::M_FINDER_DARK      => $fBorder,
            QRMatrix::M_ALIGNMENT_DARK   => $dark,
            QRMatrix::M_TIMING_DARK      => $dark,
            QRMatrix::M_FORMAT_DARK      => $dark,
            QRMatrix::M_VERSION_DARK     => $dark,
            QRMatrix::M_QUIETZONE_DARK   => $dark,
            QRMatrix::M_LOGO_DARK        => $dark,
            QRMatrix::M_FINDER_DOT       => $fEye,
            QRMatrix::M_SEPARATOR_DARK   => $light,
        ];
    }

    // ── Logo Helpers ─────────────────────────────────────────────────────────────

    /**
     * Does the user have a valid logo file configured?
     */
    protected function hasLogo()
    {
        if (empty($this->logoFile)) {
            return false;
        }
        $absPath = $this->resolveLogoPath();
        return !empty($absPath) && file_exists($absPath);
    }

    /**
     * Resolve the logo file setting to an absolute server path.
     */
    protected function resolveLogoPath()
    {
        if (empty($this->logoFile)) {
            return '';
        }
        $basePath = $this->modx->getOption('base_path', null, MODX_BASE_PATH);
        return rtrim($basePath, '/') . '/' . ltrim($this->logoFile, '/');
    }

    /**
     * Calculate the number of modules to clear for the logo.
     * Uses ~18% of the QR area (under the 20% limit enforced by the library).
     */
    protected function calculateLogoModules($matrix)
    {
        $version = $matrix->version();
        $qrSize  = $version * 4 + 17;
        // ~18% of area → sqrt(0.18 * qrSize²)
        $modules = (int) floor(sqrt(0.18 * $qrSize * $qrSize));
        // Ensure odd for centring
        if ($modules % 2 === 0) {
            $modules--;
        }
        return max(5, $modules);
    }

    /**
     * Embed an SVG logo into the centre of the QR SVG.
     */
    protected function embedLogoInSvg($svg, $matrix)
    {
        $logoPath = $this->resolveLogoPath();
        if (!file_exists($logoPath)) {
            return $svg;
        }

        $logoSvg = file_get_contents($logoPath);
        if (empty($logoSvg)) {
            return $svg;
        }

        $moduleCount = $matrix->size();
        $logoModules = $this->calculateLogoModules($matrix);

        // Centre position in the module coordinate system
        $logoX = ($moduleCount - $logoModules) / 2;
        $logoY = ($moduleCount - $logoModules) / 2;
        $logoW = $logoModules;
        $logoH = $logoModules;

        // Add a small padding inside the logo area (0.5 module)
        $pad   = 0.5;
        $innerX = $logoX + $pad;
        $innerY = $logoY + $pad;
        $innerW = $logoW - ($pad * 2);
        $innerH = $logoH - ($pad * 2);

        // Build the logo group: background rect + embedded SVG image
        $bgFill = $this->bgColor ?? '#FFFFFF';
        $logoGroup  = '';
        $logoGroup .= '<rect x="' . $logoX . '" y="' . $logoY . '" ';
        $logoGroup .= 'width="' . $logoW . '" height="' . $logoH . '" ';
        $logoGroup .= 'fill="' . $bgFill . '" />';

        // Strip the outer <svg> wrapper from the logo, keep inner content
        $innerContent = $this->extractSvgInnerContent($logoSvg);
        $logoViewBox  = $this->extractSvgViewBox($logoSvg);

        if (!empty($innerContent)) {
            $logoGroup .= '<svg x="' . $innerX . '" y="' . $innerY . '" ';
            $logoGroup .= 'width="' . $innerW . '" height="' . $innerH . '" ';
            if (!empty($logoViewBox)) {
                $logoGroup .= 'viewBox="' . $logoViewBox . '" ';
            }
            $logoGroup .= 'preserveAspectRatio="xMidYMid meet">';
            $logoGroup .= $innerContent;
            $logoGroup .= '</svg>';
        }

        // Insert logo group before closing </svg>
        $svg = str_replace('</svg>', $logoGroup . '</svg>', $svg);

        return $svg;
    }

    /**
     * Overlay the logo onto a GD PNG image.
     */
    protected function overlayLogoOnPng($gdImage, $matrix, $moduleCount, $scale)
    {
        $logoPath = $this->resolveLogoPath();
        if (!file_exists($logoPath)) {
            return $gdImage;
        }

        $logoModules = $this->calculateLogoModules($matrix);

        // Calculate pixel positions in the *final* image (after resize)
        $rawSize = $moduleCount * $scale;
        $ratio   = $this->size / $rawSize;

        $logoPxSize = (int) round($logoModules * $scale * $ratio);
        $logoPxX    = (int) round(($this->size - $logoPxSize) / 2);
        $logoPxY    = $logoPxX;

        // Draw background rectangle behind logo
        $bgFill = $this->bgColor ?? '#FFFFFF';
        $bgRgb  = $this->hexToRgb($bgFill);
        $bgGd   = imagecolorallocate($gdImage, $bgRgb[0], $bgRgb[1], $bgRgb[2]);
        imagefilledrectangle($gdImage, $logoPxX, $logoPxY, $logoPxX + $logoPxSize - 1, $logoPxY + $logoPxSize - 1, $bgGd);

        // Pad the logo slightly inside the background rect
        $pad       = (int) round($logoPxSize * 0.06);
        $innerSize = $logoPxSize - ($pad * 2);
        $innerX    = $logoPxX + $pad;
        $innerY    = $logoPxY + $pad;

        // Rasterise the SVG logo to a temporary PNG using GD
        // Since GD cannot natively render SVG, we attempt to use Imagick if available
        $logoGd = $this->rasterizeSvgForPng($logoPath, $innerSize);
        if ($logoGd !== null) {
            $srcW = imagesx($logoGd);
            $srcH = imagesy($logoGd);
            imagecopyresampled($gdImage, $logoGd, $innerX, $innerY, 0, 0, $innerSize, $innerSize, $srcW, $srcH);
            imagedestroy($logoGd);
        }

        return $gdImage;
    }

    /**
     * Attempt to rasterise an SVG logo for PNG overlay.
     * Uses Imagick if available, otherwise returns null.
     *
     * @return resource|\GdImage|null
     */
    protected function rasterizeSvgForPng($svgPath, $targetSize)
    {
        // Try Imagick first (best SVG support)
        if (extension_loaded('imagick')) {
            try {
                $im = new \Imagick();
                $im->setResolution(300, 300);
                $im->readImage($svgPath);
                $im->setImageFormat('png32');
                $im->resizeImage($targetSize, $targetSize, \Imagick::FILTER_LANCZOS, 1);
                $im->setImageBackgroundColor('transparent');

                $tmpFile = tempnam(sys_get_temp_dir(), 'slm_logo_');
                $im->writeImage('png32:' . $tmpFile);
                $im->clear();
                $im->destroy();

                $gd = imagecreatefrompng($tmpFile);
                @unlink($tmpFile);
                if ($gd !== false) {
                    imagesavealpha($gd, true);
                    return $gd;
                }
            } catch (\Exception $e) {
                $this->modx->log(\modX::LOG_LEVEL_WARN, '[ShortlinkMgr QR] Imagick SVG rasterize failed: ' . $e->getMessage());
            }
        }

        // Fallback: if a PNG version of the logo exists alongside the SVG, use it
        $pngFallback = preg_replace('/\.svg$/i', '.png', $svgPath);
        if (file_exists($pngFallback)) {
            $gd = imagecreatefrompng($pngFallback);
            if ($gd !== false) {
                imagesavealpha($gd, true);
                return $gd;
            }
        }

        return null;
    }

    // ── SVG Parsing Helpers ──────────────────────────────────────────────────────

    /**
     * Extract the inner content of an SVG file (everything between <svg> and </svg>).
     */
    protected function extractSvgInnerContent($svgString)
    {
        // Remove XML declaration if present
        $svgString = preg_replace('/<\?xml[^?]*\?>/', '', $svgString);
        // Remove comments
        $svgString = preg_replace('/<!--.*?-->/s', '', $svgString);
        // Extract inner content
        if (preg_match('/<svg[^>]*>(.*)<\/svg>/is', $svgString, $m)) {
            return trim($m[1]);
        }
        return '';
    }

    /**
     * Extract the viewBox attribute from an SVG element.
     */
    protected function extractSvgViewBox($svgString)
    {
        if (preg_match('/<svg[^>]*viewBox=["\']([^"\']+)["\'][^>]*>/i', $svgString, $m)) {
            return $m[1];
        }
        return '';
    }

    /**
     * Rewrite the SVG opening tag to set proper viewBox and explicit width/height.
     */
    protected function rewriteSvgDimensions($svg, $moduleCount)
    {
        // Replace the generated <svg> opening tag with one that has proper dimensions
        $replacement = '<svg xmlns="http://www.w3.org/2000/svg" '
                     . 'viewBox="0 0 ' . $moduleCount . ' ' . $moduleCount . '" '
                     . 'width="' . $this->size . '" height="' . $this->size . '" '
                     . 'shape-rendering="crispEdges">';

        $svg = preg_replace('/<svg[^>]*>/', $replacement, $svg, 1);

        // Remove any <defs> block that may be empty
        $svg = preg_replace('/<defs>\s*<\/defs>\s*/', '', $svg);

        return $svg;
    }

    // ── File Path Helpers ────────────────────────────────────────────────────────

    protected function getFilename($linkId, $ext)
    {
        return $this->prefix . '-' . $linkId . '.' . $ext;
    }

    protected function getSvgPath($linkId)
    {
        return $this->outputDir . $this->getFilename($linkId, 'svg');
    }

    protected function getPngPath($linkId)
    {
        return $this->outputDir . $this->getFilename($linkId, 'png');
    }

    protected function ensureOutputDir()
    {
        if (!is_dir($this->outputDir)) {
            mkdir($this->outputDir, 0775, true);
        }
    }

    // ── Colour Helpers ───────────────────────────────────────────────────────────

    /**
     * Convert a hex colour string to an [R, G, B] array.
     */
    protected function hexToRgb($hex)
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        return [
            (int) hexdec(substr($hex, 0, 2)),
            (int) hexdec(substr($hex, 2, 2)),
            (int) hexdec(substr($hex, 4, 2)),
        ];
    }
}
