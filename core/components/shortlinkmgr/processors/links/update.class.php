<?php
/**
 * ShortLink Manager - Update an existing short link
 * @package shortlinkmgr
 */
class ShortlinkmgrLinksUpdateProcessor extends modObjectUpdateProcessor {

    public $classKey       = 'ShortlinkMgrLink';
    public $languageTopics = array('shortlinkmgr:default');
    public $objectType     = 'shortlinkmgr.link';

    public function beforeSet() {
        // ── Shortcode handling ────────────────────────────────────────────────
        $shortcode = trim(strtolower($this->getProperty('shortcode', '')));
        $id = (int) $this->getProperty('id');

        if (empty($shortcode)) {
            // Auto-generate a new unique shortcode
            $length    = (int) $this->modx->getOption('shortlinkmgr.shortcode_length', null, 4);
            $shortcode = $this->generateShortcode($length);
        } else {
            // Validate format: lowercase alphanumeric only
            if (!preg_match('/^[a-z0-9]([a-z0-9\-\_\/]*[a-z0-9])?$/', $shortcode)) {
                return $this->modx->lexicon('shortlinkmgr.err_shortcode_invalid');
            }
            // Check uniqueness (allow keeping own shortcode)
            $existing = $this->modx->getObject('ShortlinkMgrLink', array('shortcode' => $shortcode));
            if ($existing && $existing->get('id') !== $id) {
                return $this->modx->lexicon('shortlinkmgr.err_shortcode_exists');
            }
        }
        $this->setProperty('shortcode', $shortcode);

        // ── Require at least one target ───────────────────────────────────────
        $redirectId  = (int) $this->getProperty('redirect_id', 0);
        $redirectUrl = trim($this->getProperty('redirect_url', ''));
        if ($redirectId <= 0 && empty($redirectUrl)) {
            return $this->modx->lexicon('shortlinkmgr.err_no_target');
        }
        $this->setProperty('redirect_id', $redirectId > 0 ? $redirectId : null);

        // ── Sanitize redirect_type ────────────────────────────────────────────
        $type = (int) $this->getProperty('redirect_type', 302);
        if (!in_array($type, array(301, 302))) $type = 302;
        $this->setProperty('redirect_type', $type);

        // ── Timestamp ────────────────────────────────────────────────────────
        $this->setProperty('updated_at', date('Y-m-d H:i:s'));

        // ── Normalize anchor ──────────────────────────────────────────────────
        $anchor = ltrim(trim($this->getProperty('anchor', '')), '#');
        $this->setProperty('anchor', $anchor);

        // ── Expires at (combine date + time fields, null if empty) ──────────
        $expDate = trim($this->getProperty('expires_at_date', ''));
        $expTime = trim($this->getProperty('expires_at_time', ''));
        if (!empty($expDate)) {
            $expiresAt = $expDate . (!empty($expTime) ? ' ' . $expTime : ' 00:00');
        } else {
            $expiresAt = '';
        }
        $this->setProperty('expires_at', !empty($expiresAt) ? $expiresAt : null);
        $this->unsetProperty('expires_at_date');
        $this->unsetProperty('expires_at_time');

        return parent::beforeSet();
    }

    // ── Shortcode generator ───────────────────────────────────────────────────
    protected function generateShortcode($length = 4) {
        $chars       = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $charsLen    = strlen($chars);
        $maxAttempts = 50;

        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            $code = '';
            for ($i = 0; $i < $length; $i++) {
                $code .= $chars[random_int(0, $charsLen - 1)];
            }
            if ($this->modx->getCount('ShortlinkMgrLink', array('shortcode' => $code)) === 0) {
                return $code;
            }
        }

        // Collision exhausted at this length — recurse with longer code
        return $this->generateShortcode($length + 1);
    }
}

return 'ShortlinkmgrLinksUpdateProcessor';
