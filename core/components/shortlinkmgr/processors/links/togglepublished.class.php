<?php
/**
 * ShortLink Manager - Toggle published state
 * @package shortlinkmgr
 */
class ShortlinkmgrLinksTogglePublishedProcessor extends modObjectUpdateProcessor {

    public $classKey       = 'ShortlinkMgrLink';
    public $languageTopics = array('shortlinkmgr:default');
    public $objectType     = 'shortlinkmgr.link';

    public function beforeSet() {
        $current = (int) $this->object->get('published');
        $this->setProperty('published', $current ? 0 : 1);
        $this->setProperty('updated_at', date('Y-m-d H:i:s'));
        return parent::beforeSet();
    }
}

return 'ShortlinkmgrLinksTogglePublishedProcessor';

