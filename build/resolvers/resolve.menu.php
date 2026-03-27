<?php
/**
 * ShortLink Manager - Menu resolver
 *
 * Attached to the PLUGIN vehicle (xPDOObjectVehicle) so it runs on both
 * install and uninstall. Never use a modMenu vehicle — the text field does
 * not survive xPDO vehicle serialization in MODX 2.x.
 *
 * The 'text' and 'description' fields MUST be literal strings, not lexicon
 * keys. The namespace lexicon is not loaded when MODX renders the top nav.
 *
 * @var xPDOTransport $transport
 * @var array         $options
 * @package shortlinkmgr
 */
if (!$transport->xpdo) return true;
$modx =& $transport->xpdo;

switch ($options[xPDOTransport::PACKAGE_ACTION]) {

    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:
        // Clean up any stale modAction records from earlier package versions.
        // modAction is deprecated in MODX 2.3+ and no longer needed.
        $staleAction = $modx->getObject('modAction', array(
            'namespace'  => 'shortlinkmgr',
            'controller' => 'index',
        ));
        if ($staleAction) {
            $staleAction->remove();
            $modx->log(modX::LOG_LEVEL_INFO, '[ShortLink Manager] Removed stale modAction record.');
        }

        // Find existing menu by namespace (supports upgrade without duplicating)
        $menu = $modx->getObject('modMenu', array('namespace' => 'shortlinkmgr'));
        if (!$menu) {
            $menu = $modx->newObject('modMenu');
            $modx->log(modX::LOG_LEVEL_INFO, '[ShortLink Manager] Creating new menu item.');
        } else {
            $modx->log(modX::LOG_LEVEL_INFO, '[ShortLink Manager] Updating existing menu item.');
        }

        // Use literal strings — the namespace lexicon is NOT loaded when MODX
        // renders the top navigation bar, so lexicon keys would show as raw keys.
        $menu->set('text',        'ShortLink Manager');
        $menu->set('parent',      'components');
        $menu->set('description', 'Manage short/vanity URLs with UTM tracking and click analytics.');
        $menu->set('icon',        'icon-link');
        $menu->set('menuindex',   0);
        $menu->set('params',      '');
        $menu->set('handler',     '');
        $menu->set('permissions', '');
        $menu->set('namespace',   'shortlinkmgr');
        $menu->set('action',      'index');

        if ($menu->save()) {
            $modx->log(modX::LOG_LEVEL_INFO, '[ShortLink Manager] Menu item saved. text=[shortlinkmgr] action=[index]');
        } else {
            $modx->log(modX::LOG_LEVEL_ERROR, '[ShortLink Manager] Menu item save FAILED.');
        }
        break;

    case xPDOTransport::ACTION_UNINSTALL:
        $menu = $modx->getObject('modMenu', array('namespace' => 'shortlinkmgr'));
        if ($menu) {
            $menu->remove();
            $modx->log(modX::LOG_LEVEL_INFO, '[ShortLink Manager] Menu item removed.');
        } else {
            $modx->log(modX::LOG_LEVEL_INFO, '[ShortLink Manager] Menu item not found, nothing to remove.');
        }

        // Also clean up any stale modAction records
        $staleAction = $modx->getObject('modAction', array(
            'namespace'  => 'shortlinkmgr',
            'controller' => 'index',
        ));
        if ($staleAction) {
            $staleAction->remove();
            $modx->log(modX::LOG_LEVEL_INFO, '[ShortLink Manager] Removed stale modAction record.');
        }
        break;
}

return true;
