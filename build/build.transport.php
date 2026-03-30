<?php
/**
 * ShortLink Manager - Transport Package Builder
 *
 * Usage (browser): build.page.html → click "Build MODX 2.x"
 * Usage (CLI):     php build.transport.php [--v2|--v3]
 *
 * Vehicle install ORDER matters:
 *   1. Namespace       (object vehicle)
 *   2. System settings (object vehicles)
 *   3. Core files      (file vehicle — source/target as object, NOT resolve())
 *   4. Assets files    (file vehicle — source/target as object, NOT resolve())
 *   5. Plugin          (object vehicle — resolvers run here, AFTER files exist)
 *
 * Resolvers are on the PLUGIN vehicle (xPDOObjectVehicle) because:
 *   - Object vehicle resolvers run on BOTH install AND uninstall
 *   - File vehicle PHP resolvers run on install only, not uninstall
 *   - Plugin is last so model files already exist when resolvers fire
 *
 * @package shortlinkmgr
 */

// ── Target version ─────────────────────────────────────────────────────────────
$isCli   = (php_sapi_name() === 'cli');
$modxVer = 2;

if ($isCli) {
    foreach ($argv as $arg) {
        if ($arg === '--v3') { $modxVer = 3; break; }
    }
} else {
    $modxVer = isset($_GET['modx_ver']) ? (int) $_GET['modx_ver'] : 2;
    header('Content-Type: text/html; charset=utf-8');
    if (ob_get_level()) ob_end_flush();
    echo '<!DOCTYPE html><html><head><meta charset="utf-8"><style>
body{background:#0d1117;color:#c9d1d9;font:13px/1.7 "Courier New",monospace;margin:0;padding:16px;}
</style></head><body><pre>';
    flush();
}

if ($modxVer === 3) {
    echo "MODX 3.x build not yet implemented.\n";
    if (!$isCli) echo '</pre></body></html>';
    exit(0);
}

// ── Bootstrap ─────────────────────────────────────────────────────────────────
ini_set('display_errors', 1);
error_reporting(E_ALL);

$modxRoot = realpath(dirname(__FILE__) . '/../../') . DIRECTORY_SEPARATOR;
if (!file_exists($modxRoot . 'config.core.php')) {
    die('ERROR: config.core.php not found at ' . $modxRoot);
}

require_once $modxRoot . 'config.core.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';

$modx = new modX();
$modx->initialize('mgr');
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget('ECHO');
flush();

echo "\n=======================================================\n";
echo " ShortLink Manager — Building MODX 2.x Transport Package\n";
echo "=======================================================\n\n";

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'build.config.php';

require_once MODX_CORE_PATH . 'model/modx/transport/modtransportpackage.class.php';
if (!class_exists('modPackageBuilder')) {
    require_once MODX_CORE_PATH . 'model/modx/transport/modpackagebuilder.class.php';
}

// Do NOT call registerNamespace() — it creates a namespace vehicle internally,
// duplicating the one we create explicitly below.
$builder = new modPackageBuilder($modx);
$builder->createPackage(PKG_NAME_LOWER, PKG_VERSION, PKG_RELEASE);

echo "Package: " . PKG_NAME . " " . PKG_VERSION . "-" . PKG_RELEASE . "\n\n";

// ── 1. Namespace ──────────────────────────────────────────────────────────────
echo "Adding namespace...\n";
$namespace = $modx->newObject('modNamespace');
$namespace->set('name',        PKG_NAMESPACE);
$namespace->set('path',        '{core_path}components/' . PKG_NAME_LOWER . '/');
$namespace->set('assets_path', '{assets_path}components/' . PKG_NAME_LOWER . '/');

$builder->putVehicle($builder->createVehicle($namespace, array(
    xPDOTransport::UNIQUE_KEY    => 'name',
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::PRESERVE_KEYS => true,
)));

// ── 2. System Settings ────────────────────────────────────────────────────────
echo "Adding system settings...\n";
$settings = include BUILD_PATH . 'data/transport.settings.php';
foreach ($settings as $setting) {
    $builder->putVehicle($builder->createVehicle($setting, array(
        xPDOTransport::UNIQUE_KEY    => 'key',
        xPDOTransport::UPDATE_OBJECT => false, // don't overwrite user-changed values on upgrade
        xPDOTransport::PRESERVE_KEYS => true,
    )));
}

// ── 2a. Manager Action ───────────────────────────────────────────────────────
// modAction is deprecated in MODX 2.3+. The menu's string 'action' field
// combined with 'namespace' is all that's needed for controller routing.
// Do NOT package a modAction — it can overwrite MODX's own action IDs and
// interfere with the manager routing.

// ── 2b. Manager Menu Item ─────────────────────────────────────────────────────
// The modMenu text field does NOT survive xPDO vehicle serialization in MODX 2.x.
// Instead, the menu is created/updated/removed by resolve.menu.php (attached to
// the plugin vehicle below) which sets fields directly via the API.

// ── 3. Core files ─────────────────────────────────────────────────────────────
// source/target must be passed as the vehicle OBJECT (first arg), not as a resolver.
// xPDOFileVehicle._installFiles() only reads $vOptions['object'] — resolvers are ignored.
echo "Adding core files...\n";
$builder->putVehicle($builder->createVehicle(
    array(
        'source' => CORE_PATH,
        'target' => "return MODX_CORE_PATH . 'components/';",
    ),
    array('vehicle_class' => 'xPDOFileVehicle')
));

// ── 4. Assets files ───────────────────────────────────────────────────────────
echo "Adding assets files...\n";
$builder->putVehicle($builder->createVehicle(
    array(
        'source' => PKG_ROOT . 'assets' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . PKG_NAME_LOWER . DIRECTORY_SEPARATOR,
        'target' => "return MODX_ASSETS_PATH . 'components/';",
    ),
    array('vehicle_class' => 'xPDOFileVehicle')
));

// ── 5. Plugin — resolvers live here ──────────────────────────────────────────
// Plugin vehicle is LAST so files exist when resolvers fire on install.
// xPDOObjectVehicle resolvers run on BOTH install and uninstall (unlike file vehicles).
// Uninstall order is reverse, so plugin uninstalls first while files still exist.
echo "Adding plugin (with resolvers)...\n";
$plugin = $modx->newObject('modPlugin');
$plugin->set('name',        'ShortLink Manager');
$plugin->set('description', 'Intercepts short URL requests and redirects to target. Handles click logging.');
$plugin->set('plugincode',  file_get_contents(CORE_PATH . 'elements/plugins/shortlinkmgr.plugin.php'));
$plugin->set('static',      false);
$plugin->set('property_preprocess', false);

$event = $modx->newObject('modPluginEvent');
$event->set('event',       'OnPageNotFound');
$event->set('priority',    0);
$event->set('propertyset', 0);
$pluginEvents = array($event);
$plugin->addMany($pluginEvents, 'PluginEvents');

$pluginVehicle = $builder->createVehicle($plugin, array(
    xPDOTransport::UNIQUE_KEY    => 'name',
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::RELATED_OBJECTS => true,
    xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array(
        'PluginEvents' => array(
            xPDOTransport::UNIQUE_KEY    => array('pluginid', 'event'),
            xPDOTransport::PRESERVE_KEYS => true,
            xPDOTransport::UPDATE_OBJECT => true,
        ),
    ),
));

// Resolvers on the plugin vehicle run on install AND uninstall
$pluginVehicle->resolve('php', array('source' => BUILD_PATH . 'resolvers/resolve.tables.php'));
$pluginVehicle->resolve('php', array('source' => BUILD_PATH . 'resolvers/resolve.menu.php'));
$builder->putVehicle($pluginVehicle);

// ── Package attributes ────────────────────────────────────────────────────────
$builder->setPackageAttributes(array(
    'changelog' => file_exists(PKG_ROOT . 'CHANGELOG.md') ? file_get_contents(PKG_ROOT . 'CHANGELOG.md') : '',
    'license'   => file_exists(PKG_ROOT . 'LICENSE')      ? file_get_contents(PKG_ROOT . 'LICENSE')      : 'MIT',
    'readme'    => file_exists(PKG_ROOT . 'README.md')    ? file_get_contents(PKG_ROOT . 'README.md')    : PKG_NAME . ' ' . PKG_VERSION,
));

// ── Pack ──────────────────────────────────────────────────────────────────────
echo "\nPacking...\n";
$builder->pack();

$packageName  = PKG_NAME_LOWER . '-' . PKG_VERSION . '-' . PKG_RELEASE . '.transport.zip';
$packagesPath = MODX_CORE_PATH . 'packages' . DIRECTORY_SEPARATOR;

// ── Write version.json (used by GitHub README badge) ─────────────────────────
$versionJsonPath = PKG_ROOT . 'version.json';
$versionJson     = json_encode(array(
    'name'    => PKG_NAME,
    'version' => PKG_VERSION . '-' . PKG_RELEASE,
), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";

if (file_put_contents($versionJsonPath, $versionJson) !== false) {
    echo "\nversion.json updated: " . PKG_VERSION . "-" . PKG_RELEASE . "\n";
} else {
    echo "\nWARNING: Could not write version.json\n";
}

// ── Auto-increment patch for next build ───────────────────────────────────────
$nextPatch       = $pkg_version_patch + 1;
$versionFilePath = BUILD_PATH . 'version.inc.php';
$versionContent  = <<<PHP
<?php
/**
 * ShortLink Manager — Current package version
 * Auto-updated by build.transport.php after each successful build.
 * Edit major/minor/release manually; patch increments automatically.
 */
\$pkg_version_major = {$pkg_version_major};
\$pkg_version_minor = {$pkg_version_minor};
\$pkg_version_patch = {$nextPatch};
\$pkg_release       = '{$pkg_release}';
PHP;

if (file_put_contents($versionFilePath, $versionContent) !== false) {
    echo "version.inc.php updated: " . PKG_VERSION . " → "
        . $pkg_version_major . '.' . $pkg_version_minor . '.' . $nextPatch . " (next build)\n";
} else {
    echo "\nWARNING: Could not write version.inc.php\n";
}

echo "\n=======================================================\n";
echo " Done! Built: " . PKG_VERSION . "-" . PKG_RELEASE . "\n";
echo " File: " . $packagesPath . $packageName . "\n";
echo "=======================================================\n";

if (!$isCli) echo '</pre></body></html>';
