<?php
/**
 * ShortLink Manager - Get a single link record
 * @package shortlinkmgr
 */
class ShortlinkmgrLinksGetProcessor extends modObjectGetProcessor {

    public $classKey       = 'ShortlinkMgrLink';
    public $languageTopics = array('shortlinkmgr:default');

    public function afterGet() {
        // Resolve resource pagetitle for display in the form
        $redirectId = (int) $this->object->get('redirect_id');
        if ($redirectId > 0) {
            $resource = $this->modx->getObject('modResource', $redirectId);
            $this->object->set('redirect_resource_title', $resource ? $resource->get('pagetitle') : '');
        }
        return parent::afterGet();
    }
}

return 'ShortlinkmgrLinksGetProcessor';
