<?php
/**
 * ShortLink Manager - System Settings transport data
 * Returns an array of modSystemSetting objects ready for packaging.
 * @package shortlinkmgr
 * @var modX $modx
 */

$settings = array();

// ── Path prefix ───────────────────────────────────────────────────────────────
$settings['path_prefix'] = $modx->newObject('modSystemSetting');
$settings['path_prefix']->fromArray(array(
    'key'       => 'shortlinkmgr.path_prefix',
    'value'     => 'go',
    'xtype'     => 'textfield',
    'namespace' => PKG_NAMESPACE,
    'area'      => 'shortlinkmgr_urls',
), '', true, true);

// ── Redirect method ───────────────────────────────────────────────────────────
$settings['redirect_method'] = $modx->newObject('modSystemSetting');
$settings['redirect_method']->fromArray(array(
    'key'       => 'shortlinkmgr.redirect_method',
    'value'     => 'plugin',
    'xtype'     => 'textfield',
    'namespace' => PKG_NAMESPACE,
    'area'      => 'shortlinkmgr_urls',
), '', true, true);

// ── Shortcode minimum length ──────────────────────────────────────────────────
$settings['shortcode_length'] = $modx->newObject('modSystemSetting');
$settings['shortcode_length']->fromArray(array(
    'key'       => 'shortlinkmgr.shortcode_length',
    'value'     => '4',
    'xtype'     => 'numberfield',
    'namespace' => PKG_NAMESPACE,
    'area'      => 'shortlinkmgr_codes',
), '', true, true);

// ── Remove tables on uninstall ────────────────────────────────────────────────
$settings['remove_table_on_uninstall'] = $modx->newObject('modSystemSetting');
$settings['remove_table_on_uninstall']->fromArray(array(
    'key'       => 'shortlinkmgr.remove_table_on_uninstall',
    'value'     => '0',
    'xtype'     => 'combo-boolean',
    'namespace' => PKG_NAMESPACE,
    'area'      => 'shortlinkmgr_advanced',
), '', true, true);

// ── QR Code Settings ─────────────────────────────────────────────────────────

// QR Code filename prefix
$settings['qr_prefix'] = $modx->newObject('modSystemSetting');
$settings['qr_prefix']->fromArray(array(
    'key'       => 'shortlinkmgr.qr_prefix',
    'value'     => 'qrcode',
    'xtype'     => 'textfield',
    'namespace' => PKG_NAMESPACE,
    'area'      => 'shortlinkmgr_qrcode',
), '', true, true);

// QR Code size (px)
$settings['qr_size'] = $modx->newObject('modSystemSetting');
$settings['qr_size']->fromArray(array(
    'key'       => 'shortlinkmgr.qr_size',
    'value'     => '800',
    'xtype'     => 'numberfield',
    'namespace' => PKG_NAMESPACE,
    'area'      => 'shortlinkmgr_qrcode',
), '', true, true);

// QR Code background colour
$settings['qr_bg_color'] = $modx->newObject('modSystemSetting');
$settings['qr_bg_color']->fromArray(array(
    'key'       => 'shortlinkmgr.qr_bg_color',
    'value'     => '#FFFFFF',
    'xtype'     => 'textfield',
    'namespace' => PKG_NAMESPACE,
    'area'      => 'shortlinkmgr_qrcode',
), '', true, true);

// QR Code default pattern colour
$settings['qr_pattern_color'] = $modx->newObject('modSystemSetting');
$settings['qr_pattern_color']->fromArray(array(
    'key'       => 'shortlinkmgr.qr_pattern_color',
    'value'     => '#000000',
    'xtype'     => 'textfield',
    'namespace' => PKG_NAMESPACE,
    'area'      => 'shortlinkmgr_qrcode',
), '', true, true);

// QR Code finder pattern outside border colour
$settings['qr_finder_border_color'] = $modx->newObject('modSystemSetting');
$settings['qr_finder_border_color']->fromArray(array(
    'key'       => 'shortlinkmgr.qr_finder_border_color',
    'value'     => '#000000',
    'xtype'     => 'textfield',
    'namespace' => PKG_NAMESPACE,
    'area'      => 'shortlinkmgr_qrcode',
), '', true, true);

// QR Code finder pattern inside eye colour
$settings['qr_finder_eye_color'] = $modx->newObject('modSystemSetting');
$settings['qr_finder_eye_color']->fromArray(array(
    'key'       => 'shortlinkmgr.qr_finder_eye_color',
    'value'     => '#000000',
    'xtype'     => 'textfield',
    'namespace' => PKG_NAMESPACE,
    'area'      => 'shortlinkmgr_qrcode',
), '', true, true);

// QR Code logo file path
$settings['qr_logo_file'] = $modx->newObject('modSystemSetting');
$settings['qr_logo_file']->fromArray(array(
    'key'       => 'shortlinkmgr.qr_logo_file',
    'value'     => '',
    'xtype'     => 'textfield',
    'namespace' => PKG_NAMESPACE,
    'area'      => 'shortlinkmgr_qrcode',
), '', true, true);

return $settings;
