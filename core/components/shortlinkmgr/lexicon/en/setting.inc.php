<?php
/**
 * ShortLink Manager - English settings lexicon
 * @package shortlinkmgr
 */

// ── Area labels ───────────────────────────────────────────────────────────────
$_lang['area_shortlinkmgr_urls']     = 'URL & Redirect Settings';
$_lang['area_shortlinkmgr_codes']    = 'Shortcode Settings';
$_lang['area_shortlinkmgr_advanced'] = 'Advanced';

// ── shortlinkmgr.path_prefix ─────────────────────────────────────────────────
$_lang['setting_shortlinkmgr.path_prefix']      = 'Short URL Path Prefix';
$_lang['setting_shortlinkmgr.path_prefix_desc'] = 'The URL segment that precedes shortcodes. Default: "go" — resulting in URLs like https://yoursite.com/go/abc1. Change with care: existing links will break if you change this after links are live. After changing, update your .htaccess if you are using the htaccess redirect method.';

// ── shortlinkmgr.redirect_method ─────────────────────────────────────────────
$_lang['setting_shortlinkmgr.redirect_method']      = 'Redirect Interception Method';
$_lang['setting_shortlinkmgr.redirect_method_desc'] = 'Controls how incoming shortcode URLs are intercepted. Allowed values: "plugin" or "htaccess".

PLUGIN METHOD (default — value: plugin):
  No .htaccess changes required. MODX Friendly URLs already route unknown paths through index.php, which fires the OnPageNotFound event. The ShortLink Manager plugin intercepts that event and redirects matching requests. Works on all hosts.

HTACCESS METHOD (value: htaccess):
  Adds an explicit Apache rewrite rule that pre-validates the shortcode URL format before passing it to MODX. This can be slightly faster because Apache rejects non-matching URLs before PHP loads. Requires you to manually add rewrite rules to your MODX root .htaccess. Add the following block BEFORE the existing MODX Friendly URLs rewrite rules:

  # --- ShortLink Manager ---
  RewriteCond %{REQUEST_URI} ^/go/([a-z0-9][a-z0-9\-\_/]*[a-z0-9]|[a-z0-9])$ [NC]
  RewriteRule ^ /index.php?q=go/%1 [QSA,L]
  # --- End ShortLink Manager ---

  Replace "go" with your configured path prefix if you changed it.

  IMPORTANT: The rule must use q=go/%1 (not slmgr_code). The q= parameter tells MODX to treat it as a Friendly URL request, which fires OnPageNotFound when no matching resource is found. The plugin then handles the redirect.

  NOTE: The ShortLink Manager plugin handles both methods. The plugin always runs on OnPageNotFound regardless of this setting. This setting is for your reference only — it reminds you whether .htaccess rules need to be maintained.';

// ── shortlinkmgr.shortcode_length ────────────────────────────────────────────
$_lang['setting_shortlinkmgr.shortcode_length']      = 'Auto-Generated Shortcode Length';
$_lang['setting_shortlinkmgr.shortcode_length_desc'] = 'Minimum number of characters for auto-generated shortcodes (lowercase alphanumeric). Default: 4. If a collision is found at this length, the generator will try longer codes automatically. Manually entered shortcodes are not affected by this setting and may be any length.';

// ── shortlinkmgr.remove_table_on_uninstall ───────────────────────────────────
$_lang['setting_shortlinkmgr.remove_table_on_uninstall']      = 'Remove Tables on Uninstall';
$_lang['setting_shortlinkmgr.remove_table_on_uninstall_desc'] = 'If enabled (Yes), the shortlinkmgr_links and shortlinkmgr_clicks database tables will be permanently dropped when the package is uninstalled. ALL short link data and click logs will be lost. Default: No (tables are preserved on uninstall so data survives reinstalls and upgrades).';

// ── Area label ───────────────────────────────────────────────────────────────
$_lang['area_shortlinkmgr_qrcode'] = 'QR Code';

// ── shortlinkmgr.qr_prefix ──────────────────────────────────────────────────
$_lang['setting_shortlinkmgr.qr_prefix']      = 'QR Code Filename Prefix';
$_lang['setting_shortlinkmgr.qr_prefix_desc'] = 'Prefix used when naming generated QR code files. Files are saved as {prefix}-{id}.svg and {prefix}-{id}.png in the assets/components/shortlinkmgr/qr-codes/ directory. Default: "qrcode" (producing filenames like qrcode-42.svg). Use only lowercase letters, numbers, and hyphens.';

// ── shortlinkmgr.qr_size ────────────────────────────────────────────────────
$_lang['setting_shortlinkmgr.qr_size']      = 'QR Code Size (px)';
$_lang['setting_shortlinkmgr.qr_size_desc'] = 'Width and height of the generated QR code image in pixels. QR codes are always square so only one dimension is needed. This value controls both the SVG viewBox dimensions and the PNG pixel size. Default: 800. Recommended range: 200–2000. Larger sizes produce higher-resolution images suitable for print.';

// ── shortlinkmgr.qr_bg_color ────────────────────────────────────────────────
$_lang['setting_shortlinkmgr.qr_bg_color']      = 'QR Code Background Colour';
$_lang['setting_shortlinkmgr.qr_bg_color_desc'] = 'Background colour of the QR code as a hex value (e.g. #FFFFFF for white, #F0F0F0 for light grey). Leave this field empty for a transparent background. Default: #FFFFFF (white). Note: transparent backgrounds may reduce scannability on dark surfaces — white or a light colour is recommended for best results.';

// ── shortlinkmgr.qr_pattern_color ───────────────────────────────────────────
$_lang['setting_shortlinkmgr.qr_pattern_color']      = 'Default Pattern Colour';
$_lang['setting_shortlinkmgr.qr_pattern_color_desc'] = 'The main colour used for the data modules (small squares) that make up the QR code pattern, as well as timing, alignment, and format modules. Enter a hex colour value (e.g. #000000 for black, #3A4B5C for dark blue-grey). If left empty, falls back to #000000 (black). Default: #000000.';

// ── shortlinkmgr.qr_finder_border_color ─────────────────────────────────────
$_lang['setting_shortlinkmgr.qr_finder_border_color']      = 'Finder Pattern Border Colour';
$_lang['setting_shortlinkmgr.qr_finder_border_color_desc'] = 'Colour of the outer border ring of the three large finder squares located in the top-left, top-right, and bottom-left corners of the QR code. This is the outermost dark ring of each finder pattern. Enter a hex colour value. If left empty, falls back to #000000 (black). Default: #000000. Tip: use the same colour as the default pattern for a standard look, or a different colour for a branded appearance.';

// ── shortlinkmgr.qr_finder_eye_color ────────────────────────────────────────
$_lang['setting_shortlinkmgr.qr_finder_eye_color']      = 'Finder Pattern Eye Colour';
$_lang['setting_shortlinkmgr.qr_finder_eye_color_desc'] = 'Colour of the small solid square (the "eye") at the centre of each of the three finder patterns. This is the innermost 3×3 module block inside the finder squares. Enter a hex colour value. If left empty, falls back to #000000 (black). Default: #000000. Tip: a contrasting colour here (e.g. a brand accent colour) can add a branded look while keeping the code scannable.';

// ── shortlinkmgr.qr_logo_file ───────────────────────────────────────────────
$_lang['setting_shortlinkmgr.qr_logo_file']      = 'QR Code Centre Logo';
$_lang['setting_shortlinkmgr.qr_logo_file_desc'] = 'Relative site path to a square SVG file that will be embedded in the centre of the QR code for branding (e.g. assets/images/logo-qr.svg). The logo is placed over the centre of the QR code at the maximum size that still allows the code to be scanned reliably — the generator automatically uses the highest error correction level (H, 30% recovery) when a logo is present. The QR code background colour is drawn behind the logo. If the logo SVG has its own background, it will be used as-is. Leave empty for a standard QR code with no logo. Default: empty (no logo).';

