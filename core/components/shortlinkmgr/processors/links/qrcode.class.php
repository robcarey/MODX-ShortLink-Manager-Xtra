<?php
/**
 * ShortLink Manager - QR Code Processor
 *
 * Generates (or returns cached) QR code files for a given shortlink.
 *
 * POST parameters:
 *   id           int    — ShortlinkMgrLink record ID (required)
 *   regenerate   bool   — Force regeneration even if files exist (optional)
 *
 * @package shortlinkmgr
 */

class ShortlinkmgrLinksQrcodeProcessor extends modProcessor
{
    public $languageTopics = array('shortlinkmgr:default');

    public function process()
    {
        $id = (int) $this->getProperty('id', 0);
        if ($id < 1) {
            return $this->failure($this->modx->lexicon('shortlinkmgr.err_qr_no_id'));
        }

        // Load the shortlink record
        $link = $this->modx->getObject('ShortlinkMgrLink', $id);
        if (!$link) {
            return $this->failure($this->modx->lexicon('shortlinkmgr.err_not_found'));
        }

        // Build the full short URL
        $siteUrl    = rtrim($this->modx->getOption('site_url'), '/');
        $pathPrefix = $this->modx->getOption('shortlinkmgr.path_prefix', null, 'go');
        $shortcode  = $link->get('shortcode');
        $shortUrl   = $siteUrl . '/' . $pathPrefix . '/' . $shortcode;

        // Load the QR generator
        $corePath = $this->modx->getOption(
            'shortlinkmgr.core_path',
            null,
            $this->modx->getOption('core_path') . 'components/shortlinkmgr/'
        );
        require_once $corePath . 'lib/ShortlinkmgrQRGenerator.php';

        $generator  = new ShortlinkmgrQRGenerator($this->modx);
        $regenerate = (bool) $this->getProperty('regenerate', false);

        // Generate or return cached
        if ($regenerate || !$generator->filesExist($id)) {
            try {
                $result = $generator->generate($shortUrl, $id);
            } catch (\Exception $e) {
                $this->modx->log(\modX::LOG_LEVEL_ERROR, '[ShortlinkMgr QR] Generation failed: ' . $e->getMessage());
                return $this->failure($this->modx->lexicon('shortlinkmgr.err_qr_generate') . ' ' . $e->getMessage());
            }
        } else {
            $urls   = $generator->getFileUrls($id);
            $result = array_merge($urls, [
                'svg_content' => $generator->getSvgContent($id),
                'short_url'   => $shortUrl,
            ]);
        }

        return $this->success('', $result);
    }
}

return 'ShortlinkmgrLinksQrcodeProcessor';
