<?php
/**
 * ShortLink Manager - CMP Index Controller
 *
 * Loaded by MODX when accessing: manager/?namespace=shortlinkmgr&action=index
 *
 * @package shortlinkmgr
 */
class ShortlinkmgrIndexManagerController extends modManagerController {

    /** @var string Base URL for assets */
    public $assetsUrl;

    /** @var string Base path for core files */
    public $corePath;

    public function initialize() {
        $this->corePath  = $this->modx->getOption(
            'shortlinkmgr.core_path',
            null,
            $this->modx->getOption('core_path') . 'components/shortlinkmgr/'
        );
        $this->assetsUrl = $this->modx->getOption(
            'shortlinkmgr.assets_url',
            null,
            $this->modx->getOption('assets_url') . 'components/shortlinkmgr/'
        );

        $this->modx->addPackage('shortlinkmgr', $this->corePath . 'model/');
        $this->modx->lexicon->load('shortlinkmgr:default');

        return true;
    }

    public function getLanguageTopics() {
        return array('shortlinkmgr:default');
    }

    public function checkPermissions() {
        return true;
    }

    public function process(array $scriptProperties = array()) {}

    public function getPageTitle() {
        return $this->modx->lexicon('shortlinkmgr');
    }

    public function loadCustomCssJs() {
        $this->addCss($this->assetsUrl . 'css/mgr/shortlinkmgr.css');

        $this->addJavascript($this->assetsUrl . 'js/mgr/shortlinkmgr.js');
        $this->addJavascript($this->assetsUrl . 'js/mgr/widgets/grid.links.js');
        $this->addJavascript($this->assetsUrl . 'js/mgr/widgets/window.link.js');
        $this->addJavascript($this->assetsUrl . 'js/mgr/widgets/panel.home.js');
        $this->addLastJavascript($this->assetsUrl . 'js/mgr/sections/index.js');

        $this->addHtml('<script>
Shortlinkmgr.config = ' . json_encode(array(
            'connector_url' => $this->modx->getOption('assets_url') . 'components/shortlinkmgr/connector.php',
            'assets_url'    => $this->assetsUrl,
            'path_prefix'   => $this->modx->getOption('shortlinkmgr.path_prefix', null, 'go'),
            'base_url'      => $this->modx->getOption('site_url'),
        )) . ';
</script>');
    }

    public function getTemplateFile() {
        return $this->corePath . 'templates/index.tpl';
    }
}
