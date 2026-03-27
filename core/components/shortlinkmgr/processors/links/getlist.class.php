<?php
/**
 * ShortLink Manager - Get paginated list of links
 * Called by the ExtJS grid store.
 * @package shortlinkmgr
 */
class ShortlinkmgrLinksGetListProcessor extends modObjectGetListProcessor {

    public $classKey         = 'ShortlinkMgrLink';
    public $languageTopics   = array('shortlinkmgr:default');
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'DESC';

    public function prepareQueryBeforeCount(xPDOQuery $c) {
        // Bulk-unpublish any published links that have passed their expiry date
        $now = date('Y-m-d H:i:s');
        $expired = $this->modx->newQuery('ShortlinkMgrLink');
        $expired->where(array(
            'published'     => 1,
            'expires_at:!=' => '',
            'expires_at:<'  => $now,
        ));
        $expired->where('expires_at IS NOT NULL');
        $expiredLinks = $this->modx->getCollection('ShortlinkMgrLink', $expired);
        foreach ($expiredLinks as $link) {
            $link->set('published', 0);
            $link->save();
        }

        $search = $this->getProperty('search', '');
        if (!empty($search)) {
            $c->where(array(
                'shortcode:LIKE'    => '%' . $search . '%',
                'OR:title:LIKE'     => '%' . $search . '%',
                'OR:redirect_url:LIKE' => '%' . $search . '%',
            ));
        }

        // Filter by published state if requested
        $published = $this->getProperty('published', '');
        if ($published !== '') {
            $c->where(array('published' => (int) $published));
        }

        return $c;
    }

    public function prepareRow(xPDOObject $object) {
        $row = $object->toArray();

        // Build preview short URL for display
        $prefix  = $this->modx->getOption('shortlinkmgr.path_prefix', null, 'go');
        $siteUrl = rtrim($this->modx->getOption('site_url'), '/');
        $row['short_url'] = $siteUrl . '/' . $prefix . '/' . $row['shortcode'];

        // Resolve resource title if redirect_id is set
        if (!empty($row['redirect_id'])) {
            $resource = $this->modx->getObject('modResource', (int) $row['redirect_id']);
            $row['redirect_resource_title'] = $resource ? $resource->get('pagetitle') : '(not found)';
        } else {
            $row['redirect_resource_title'] = '';
        }

        // Format dates for display
        if (!empty($row['expires_at'])) {
            $row['expires_at_display'] = $row['expires_at'];
        } else {
            $row['expires_at_display'] = '—';
        }

        // Is the link currently expired?
        $row['is_expired'] = (!empty($row['expires_at']) && strtotime($row['expires_at']) < time());

        return $row;
    }
}

return 'ShortlinkmgrLinksGetListProcessor';
