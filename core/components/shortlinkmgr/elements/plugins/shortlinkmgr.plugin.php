<?php
/**
 * ShortLink Manager Plugin
 *
 * Intercepts requests matching /{prefix}/{shortcode} and issues the configured
 * redirect. Fires on OnPageNotFound.
 *
 * Only active when the system setting shortlinkmgr.redirect_method = "plugin"
 * (the default). When set to "htaccess" this plugin exits immediately so you
 * can switch methods without uninstalling the plugin.
 *
 * @package shortlinkmgr
 * @event   OnPageNotFound
 */

// Only handle the correct event
if ($modx->event->name !== 'OnPageNotFound') return;

// ── Parse the request URI ─────────────────────────────────────────────────────
$prefix  = trim($modx->getOption('shortlinkmgr.path_prefix', null, 'go'), '/');
$requestUri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';

// Strip query string for path matching
$path = parse_url($requestUri, PHP_URL_PATH);
if ($path === false || $path === null) return;

$path  = trim($path, '/');
$parts = explode('/', $path, 2);

// Must have exactly prefix + shortcode
if (count($parts) < 2 || $parts[0] !== $prefix) return;

$shortcode = strtolower(trim($parts[1]));
if (empty($shortcode)) return;

// ── Load model ────────────────────────────────────────────────────────────────
$corePath = $modx->getOption(
    'shortlinkmgr.core_path',
    null,
    $modx->getOption('core_path') . 'components/shortlinkmgr/'
);
$modx->addPackage('shortlinkmgr', $corePath . 'model/');

// ── Look up the shortcode ─────────────────────────────────────────────────────
$link = $modx->getObject('ShortlinkMgrLink', array(
    'shortcode' => $shortcode,
    'published' => 1,
));

if (!$link) return; // Not found — let MODX serve its normal 404

// ── Check expiry ──────────────────────────────────────────────────────────────
$expiresAt = $link->get('expires_at');
if (!empty($expiresAt) && strtotime($expiresAt) < time()) {
    // Auto-unpublish so the grid reflects the expired state
    $link->set('published', 0);
    $link->save();
    return; // Expired — serve 404
}

// ── Resolve target URL ────────────────────────────────────────────────────────
$targetUrl  = '';
$redirectId = (int) $link->get('redirect_id');

if ($redirectId > 0) {
    $resource = $modx->getObject('modResource', array('id' => $redirectId, 'published' => 1));
    if ($resource) {
        $targetUrl = $modx->makeUrl($redirectId, '', '', 'full');
    }
}

// Fallback to redirect_url
if (empty($targetUrl)) {
    $targetUrl = trim($link->get('redirect_url'));
}

if (empty($targetUrl)) return; // No valid target — serve 404

// ── Build query string ────────────────────────────────────────────────────────
$params = array();

$utmFields = array('utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content');
foreach ($utmFields as $field) {
    $val = $link->get($field);
    if (!empty($val)) {
        $params[$field] = $val;
    }
}

$additionalParams = $link->get('additional_params');
if (!empty($additionalParams)) {
    parse_str($additionalParams, $extra);
    $params = array_merge($params, $extra);
}

if (!empty($params)) {
    $separator  = (strpos($targetUrl, '?') !== false) ? '&' : '?';
    $targetUrl .= $separator . http_build_query($params);
}

// ── Append anchor ─────────────────────────────────────────────────────────────
$anchor = ltrim(trim($link->get('anchor')), '#');
if (!empty($anchor)) {
    $targetUrl .= '#' . $anchor;
}

// ── Log the click ─────────────────────────────────────────────────────────────
$click = $modx->newObject('ShortlinkMgrClick');
$click->fromArray(array(
    'link_id'    => $link->get('id'),
    'clicked_at' => date('Y-m-d H:i:s'),
    'ip_address' => isset($_SERVER['HTTP_X_FORWARDED_FOR'])
                        ? trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0])
                        : (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : ''),
    'referrer'   => isset($_SERVER['HTTP_REFERER'])   ? substr($_SERVER['HTTP_REFERER'],   0, 2048) : '',
    'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 512)  : '',
));
$click->save();

// Increment click counter (best-effort; non-critical)
$link->set('click_count', (int) $link->get('click_count') + 1);
$link->save();

// ── Issue redirect ────────────────────────────────────────────────────────────
$redirectType = (int) $link->get('redirect_type');
if ($redirectType === 301) {
    header('HTTP/1.1 301 Moved Permanently');
} else {
    header('HTTP/1.1 302 Found');
}
header('Location: ' . $targetUrl);

// Stop MODX from doing anything else
$modx->event->stopPropagation();
exit();
