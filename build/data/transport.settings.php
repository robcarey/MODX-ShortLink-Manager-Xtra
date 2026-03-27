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

return $settings;
