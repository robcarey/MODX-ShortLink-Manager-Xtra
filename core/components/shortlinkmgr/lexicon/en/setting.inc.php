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
