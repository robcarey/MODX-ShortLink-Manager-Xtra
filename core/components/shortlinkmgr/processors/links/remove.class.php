<?php
/**
 * ShortLink Manager - Remove a short link
 * Also removes associated click logs (cascading delete in PHP, not relying on DB FK).
 * @package shortlinkmgr
 */
class ShortlinkmgrLinksRemoveProcessor extends modObjectRemoveProcessor {

    public $classKey       = 'ShortlinkMgrLink';
    public $languageTopics = array('shortlinkmgr:default');
    public $objectType     = 'shortlinkmgr.link';

    public function beforeRemove() {
        // Delete all associated click records first
        $linkId = (int) $this->object->get('id');
        $this->modx->removeCollection('ShortlinkMgrClick', array('link_id' => $linkId));
        return parent::beforeRemove();
    }
}

return 'ShortlinkmgrLinksRemoveProcessor';
