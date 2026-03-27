# ShortLink Manager for MODX

A full-featured short link management extra for MODX Revolution. Create branded short URLs, track clicks, append UTM parameters, and manage link expiry — all from a clean Custom Manager Page.

## Features

- **Short URL Management** — Create and manage short links like `yoursite.com/go/promo1`
- **Auto-Generated Shortcodes** — Unique codes generated automatically, or enter your own
- **Flexible Redirect Targets** — Point to a MODX resource (by ID) or any external URL
- **301 & 302 Redirects** — Choose permanent or temporary redirects per link
- **UTM Campaign Tracking** — Built-in fields for all five UTM parameters, plus custom query params and URL anchors
- **Click Analytics** — Every click is logged with timestamp, IP, referrer, and user agent
- **Link Expiry** — Set an optional expiry date/time; expired links auto-unpublish
- **Publish/Unpublish** — Toggle links on and off without deleting them
- **Searchable Grid** — Paginated, sortable, and searchable from the MODX manager

## Requirements

- MODX Revolution 2.8+
- PHP 8.0+
- MySQL 5.7+ or MariaDB 10.3+

## Installation

1. Download the transport package (`shortlinkmgr-1.0.0-pl.transport.zip`)
2. In the MODX manager, go to **Extras → Installer**
3. Upload the package and install
4. The **ShortLink Manager** menu item appears under **Extras**

No database setup required — tables are created automatically on install.

## Configuration

System settings are under **Settings → System Settings → ShortLink Manager**:

| Setting | Default | Description |
|---------|---------|-------------|
| `shortlinkmgr.path_prefix` | `go` | URL segment before the shortcode (e.g., `/go/abc1`) |
| `shortlinkmgr.redirect_method` | `plugin` | How requests are intercepted: `plugin` or `htaccess` |
| `shortlinkmgr.shortcode_length` | `4` | Minimum length for auto-generated shortcodes |
| `shortlinkmgr.remove_table_on_uninstall` | `No` | Whether to drop data tables when uninstalling |

## Usage

### Creating a Short Link

1. Go to **Extras → ShortLink Manager**
2. Click **Add Short Link**
3. Fill in the form:
   - **Shortcode** — Leave blank to auto-generate, or enter your own (lowercase alphanumeric)
   - **Title** — Internal label for your reference
   - **Resource ID** — Target MODX resource, OR
   - **External URL** — Any URL (used as fallback if Resource ID is empty)
   - **Redirect Type** — 301 (permanent) or 302 (temporary)
4. Optionally fill in UTM parameters and set an expiry date
5. Save

### Redirect Methods

**Plugin mode** (default) — Works out of the box. The MODX plugin intercepts `OnPageNotFound` events and handles the redirect. No server configuration needed.

**htaccess mode** — Add the following to your `.htaccess` file, **before** the MODX Friendly URLs rules:

```apache
# --- ShortLink Manager ---
RewriteCond %{REQUEST_URI} ^/go/([a-z0-9]+)$ [NC]
RewriteRule ^ /index.php?q=go/%1 [QSA,L]
# --- End ShortLink Manager ---
```

Replace `go` with your configured path prefix if you changed it.

### Link Expiry

Set an expiry date and time on any link. Once expired:
- Visitors receive a 404 and the link is automatically unpublished
- The CMP grid also bulk-unpublishes expired links on every load
- Re-publish a link at any time by toggling it back on (and clearing/extending the expiry)

## Uninstalling

By default, uninstalling preserves your data tables so links and click history survive a reinstall or upgrade. To remove all data, set `shortlinkmgr.remove_table_on_uninstall` to `Yes` before uninstalling.

## License

MIT — see [LICENSE](LICENSE) for details.

