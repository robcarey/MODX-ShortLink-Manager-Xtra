<?php
/**
 * ShortLink Manager - AJAX Connector
 *
 * All CMP grid/form AJAX requests are routed through this file.
 *
 * @package shortlinkmgr
 * @var modX $modx
 */

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';

$corePath = $modx->getOption(
    'shortlinkmgr.core_path',
    null,
    $modx->getOption('core_path') . 'components/shortlinkmgr/'
);
$modx->addPackage('shortlinkmgr', $corePath . 'model/');
$modx->lexicon->load('shortlinkmgr:default');

/* handle request */
$modx->request->handleRequest(array(
    'processors_path' => $corePath . 'processors/',
    'location'        => '',
));
