<?php
/**
 * ShortLink Manager - Build Configuration
 *
 * Directory layout (relative to MODX root):
 *   _shortlink/build/          <- this file lives here
 *   _shortlink/core/components/shortlinkmgr/
 *   _shortlink/assets/components/shortlinkmgr/
 *
 * MODX root is three levels up from this file:
 *   build/ -> _shortlink/ -> [modx root]
 */

// ── Package metadata ─────────────────────────────────────────────────────────
define('PKG_NAME',       'ShortLink Manager');
define('PKG_NAME_LOWER', 'shortlinkmgr');
define('PKG_NAMESPACE',  'shortlinkmgr');

// Version is managed in version.inc.php and auto-incremented by the build script.
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'version.inc.php';
define('PKG_VERSION', $pkg_version_major . '.' . $pkg_version_minor . '.' . $pkg_version_patch);
define('PKG_RELEASE', $pkg_release);

// ── Directory paths ───────────────────────────────────────────────────────────
// Absolute path to the _shortlink/ root
define('PKG_ROOT',    realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..') . DIRECTORY_SEPARATOR);

// Absolute path to the MODX installation root
define('MODX_ROOT',   realpath(PKG_ROOT . '..') . DIRECTORY_SEPARATOR);

// Build directory
define('BUILD_PATH',  PKG_ROOT . 'build' . DIRECTORY_SEPARATOR);

// Source trees that will be copied into the transport package
define('CORE_PATH',   PKG_ROOT . 'core'   . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . PKG_NAME_LOWER . DIRECTORY_SEPARATOR);
define('ASSETS_PATH', PKG_ROOT . 'assets' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . PKG_NAME_LOWER . DIRECTORY_SEPARATOR);
