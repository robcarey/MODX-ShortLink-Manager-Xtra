<?php
/**
 * ShortLink Manager - Import links from CSV
 *
 * Expects a multipart/form-data POST with a "file" field containing a CSV.
 * The CSV must have a header row. Recognised columns (case-insensitive):
 *   shortcode, title, description, published, redirect_id, redirect_url,
 *   redirect_type, utm_source, utm_medium, utm_campaign, utm_term,
 *   utm_content, anchor, additional_params, expires_at
 *
 * @package shortlinkmgr
 */
class ShortlinkmgrLinksImportProcessor extends modProcessor {

    public $languageTopics = array('shortlinkmgr:default');

    /** @var array Columns we accept from the CSV */
    protected $allowedColumns = array(
        'shortcode', 'title', 'description', 'published',
        'redirect_id', 'redirect_url', 'redirect_type',
        'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content',
        'anchor', 'additional_params', 'expires_at',
    );

    public function initialize() {
        $this->modx->addPackage('shortlinkmgr',
            $this->modx->getOption('shortlinkmgr.core_path', null,
                $this->modx->getOption('core_path') . 'components/shortlinkmgr/') . 'model/'
        );
        return parent::initialize();
    }

    public function process() {
        // ── Validate upload ──────────────────────────────────────────────────
        if (empty($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            return $this->failure($this->modx->lexicon('shortlinkmgr.import_err_no_file'));
        }

        $tmpFile = $_FILES['file']['tmp_name'];
        $handle  = fopen($tmpFile, 'r');
        if (!$handle) {
            return $this->failure($this->modx->lexicon('shortlinkmgr.import_err_read'));
        }

        // ── Read header row ──────────────────────────────────────────────────
        $headerRaw = fgetcsv($handle);
        if (!$headerRaw || count($headerRaw) < 2) {
            fclose($handle);
            return $this->failure($this->modx->lexicon('shortlinkmgr.import_err_header'));
        }

        // Normalise headers: lowercase, trim, strip BOM from first column
        $headers = array_map(function ($h) {
            return strtolower(trim(preg_replace('/^\x{FEFF}/u', '', $h)));
        }, $headerRaw);

        // Map header positions to allowed columns
        $colMap = array();
        foreach ($headers as $idx => $name) {
            if (in_array($name, $this->allowedColumns, true)) {
                $colMap[$name] = $idx;
            }
        }

        if (empty($colMap)) {
            fclose($handle);
            return $this->failure($this->modx->lexicon('shortlinkmgr.import_err_header'));
        }

        // ── Process rows ─────────────────────────────────────────────────────
        $imported = 0;
        $skipped  = 0;
        $errors   = array();
        $row      = 1; // header was row 0
        $now      = date('Y-m-d H:i:s');
        $userId   = $this->modx->user->get('id');
        $defaultLen = (int) $this->modx->getOption('shortlinkmgr.shortcode_length', null, 4);

        while (($data = fgetcsv($handle)) !== false) {
            $row++;

            // Build field array from CSV columns, converting "NULL" → null
            $fields = array();
            foreach ($colMap as $name => $idx) {
                $val = isset($data[$idx]) ? trim($data[$idx]) : '';
                $fields[$name] = (strcasecmp($val, 'NULL') === 0 || $val === '') ? null : $val;
            }

            // ── Shortcode ────────────────────────────────────────────────────
            $shortcode = strtolower(trim($fields['shortcode'] ?? ''));
            if (empty($shortcode)) {
                $shortcode = $this->generateShortcode($defaultLen);
            } elseif (!preg_match('/^[a-z0-9]([a-z0-9\-\_\/]*[a-z0-9])?$/', $shortcode)) {
                $errors[] = "Row {$row}: invalid shortcode \"{$shortcode}\"";
                $skipped++;
                continue;
            }

            // Duplicate check
            if ($this->modx->getCount('ShortlinkMgrLink', array('shortcode' => $shortcode)) > 0) {
                $errors[] = "Row {$row}: shortcode \"{$shortcode}\" already exists";
                $skipped++;
                continue;
            }

            // ── Resolve redirect target ──────────────────────────────────────
            $redirectId  = (int) ($fields['redirect_id'] ?? 0);
            $redirectUrl = $fields['redirect_url'] ?? '';

            // Auto-detect: if redirect_url is purely numeric, treat as resource ID
            if ($redirectId <= 0 && !empty($redirectUrl) && ctype_digit((string) $redirectUrl)) {
                $redirectId  = (int) $redirectUrl;
                $redirectUrl = '';
            }

            if ($redirectId <= 0 && empty($redirectUrl)) {
                $errors[] = "Row {$row}: no redirect target";
                $skipped++;
                continue;
            }

            // ── Build object ─────────────────────────────────────────────────
            $link = $this->modx->newObject('ShortlinkMgrLink');
            $link->fromArray(array(
                'shortcode'         => $shortcode,
                'title'             => $fields['title'] ?? '',
                'description'       => $fields['description'] ?? null,
                'published'         => isset($fields['published']) ? (int) $fields['published'] : 1,
                'redirect_id'       => $redirectId > 0 ? $redirectId : null,
                'redirect_url'      => !empty($redirectUrl) ? $redirectUrl : null,
                'redirect_type'     => in_array((int) ($fields['redirect_type'] ?? 302), array(301, 302)) ? (int) $fields['redirect_type'] : 302,
                'utm_source'        => $fields['utm_source'] ?? null,
                'utm_medium'        => $fields['utm_medium'] ?? null,
                'utm_campaign'      => $fields['utm_campaign'] ?? null,
                'utm_term'          => $fields['utm_term'] ?? null,
                'utm_content'       => $fields['utm_content'] ?? null,
                'anchor'            => ltrim($fields['anchor'] ?? '', '#'),
                'additional_params' => $fields['additional_params'] ?? null,
                'expires_at'        => !empty($fields['expires_at']) ? $fields['expires_at'] : null,
                'click_count'       => 0,
                'created_by'        => $userId,
                'created_at'        => $now,
                'updated_at'        => $now,
            ));

            if ($link->save()) {
                $imported++;
            } else {
                $errors[] = "Row {$row}: database save failed";
                $skipped++;
            }
        }

        fclose($handle);

        // ── Build result message ─────────────────────────────────────────────
        $msg = sprintf($this->modx->lexicon('shortlinkmgr.import_success'), $imported, $skipped);
        if (!empty($errors)) {
            $msg .= "\n" . implode("\n", array_slice($errors, 0, 20));
        }

        return $this->success($msg);
    }

    // ── Shortcode generator (same logic as create processor) ────────────────
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

        return $this->generateShortcode($length + 1);
    }
}

return 'ShortlinkmgrLinksImportProcessor';
