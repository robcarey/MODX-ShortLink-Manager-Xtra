# ShortLink Manager for MODX

![Version](https://img.shields.io/badge/dynamic/json?url=https%3A%2F%2Fraw.githubusercontent.com%2Frobcarey%2FMODX-ShortLink-Manager-Xtra%2Fmaster%2Fversion.json&query=%24.version&label=version&color=blue)
![PHP](https://img.shields.io/badge/PHP-8.1%2B-777BB4)
![MODX](https://img.shields.io/badge/MODX-2.8%2B-3FAD50)
![License](https://img.shields.io/github/license/robcarey/MODX-ShortLink-Manager-Xtra)

A full-featured short link management extra for MODX Revolution. Create branded short URLs, track clicks, append UTM parameters, manage link expiry, and generate branded QR codes — all from a clean Custom Manager Page.

## Screenshots

### Basic Information
Configure your shortcode, title, redirect target (MODX resource or external URL), and redirect type — all from a clean tabbed interface.

![Basic Information](screenshots/edit-add-dialog-tab1.png)

### UTM Parameters
Built-in fields for all five Google Analytics UTM parameters. Values are automatically appended to the redirect URL.

![UTM Parameters](screenshots/edit-add-dialog-tab2.png)

### Advanced Options
Set a URL anchor, additional query parameters, and an optional expiry date/time for automatic link deactivation.

![Advanced Options](screenshots/edit-add-dialog-tab3.png)

### QR Code Generation
Generate branded QR codes with custom colours for data patterns, finder borders, and finder eyes. Embed a centre logo (SVG) and download as SVG (vector) or PNG.

![QR Code](screenshots/edit-add-dialog-tab4.png)

### System Settings
All settings are organised by area — QR Code appearance, URL configuration, shortcode behaviour, and advanced options — each with detailed descriptions.

![System Settings](screenshots/system-settings.png)

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
- **QR Code Generation** — Generate branded QR codes for any short link, with customisable colours, finder pattern styling, and optional centre logo

## Requirements

- MODX Revolution 2.8+
- PHP 8.1+
- MySQL 5.7+ or MariaDB 10.3+
- ext-gd (recommended, for PNG export)

## Installation

1. Download the transport package (`shortlinkmgr-x.x.x-pl.transport.zip`)
2. In the MODX manager, go to **Extras → Installer**
3. Upload the package and install
4. The **ShortLink Manager** menu item appears under **Extras**

No database setup required — tables are created automatically on install.

## Configuration

System settings are under **Settings → System Settings → ShortLink Manager**.

### URL & Redirect Settings

| Setting | Default | Description |
|---------|---------|-------------|
| `shortlinkmgr.path_prefix` | `go` | URL segment before the shortcode (e.g., `/go/abc1`) |
| `shortlinkmgr.redirect_method` | `plugin` | How requests are intercepted: `plugin` or `htaccess` |

### Shortcode Settings

| Setting | Default | Description |
|---------|---------|-------------|
| `shortlinkmgr.shortcode_length` | `4` | Minimum length for auto-generated shortcodes |

### QR Code Settings

| Setting | Default | Description |
|---------|---------|-------------|
| `shortlinkmgr.qr_prefix` | `qrcode` | Filename prefix for generated files (e.g., `qrcode-42.svg`) |
| `shortlinkmgr.qr_size` | `800` | Width/height in pixels (QR codes are always square) |
| `shortlinkmgr.qr_bg_color` | `#FFFFFF` | Background colour (hex). Leave empty for transparent |
| `shortlinkmgr.qr_pattern_color` | `#000000` | Colour of the data modules (small squares). Falls back to black if empty |
| `shortlinkmgr.qr_finder_border_color` | `#000000` | Colour of the outer border on the three finder squares |
| `shortlinkmgr.qr_finder_eye_color` | `#000000` | Colour of the centre eye inside the three finder squares |
| `shortlinkmgr.qr_logo_file` | *(empty)* | Relative path to a square SVG logo to embed in the QR code centre |

### Advanced Settings

| Setting | Default | Description |
|---------|---------|-------------|
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

### QR Codes

The **QR Code** tab appears when editing an existing short link. It is not shown when creating a new link (the shortcode must exist first so the full URL can be encoded).

**Generating a QR code:**

1. Open an existing short link for editing
2. Click the **QR Code** tab
3. The QR code generates automatically on first visit, or click **Generate QR Code**
4. Download the **SVG** (full vector, ideal for print) or **PNG** (raster) version
5. Click **Regenerate** after changing QR settings to rebuild with the new colours/logo

**Branded QR codes:**

Customise the appearance via the QR Code system settings:

- Set different colours for the data pattern, finder pattern borders, and finder eye centres
- Add a centre logo by pointing `shortlinkmgr.qr_logo_file` to a square SVG file (e.g., `assets/images/logo-qr.svg`)
- When a logo is configured, the generator automatically uses the highest error correction level (H, 30% recovery) to ensure the code remains scannable
- The logo is centred at the maximum safe size and backed by the configured background colour

Generated files are saved to `assets/components/shortlinkmgr/qr-codes/` as `{prefix}-{id}.svg` and `{prefix}-{id}.png`. Files are cached — they are only regenerated when you click Regenerate, and are automatically cleaned up when a short link is deleted.

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

## Third-Party Libraries

This package bundles [chillerlan/php-qrcode](https://github.com/chillerlan/php-qrcode) v4.x (MIT License) for QR code generation. See `core/components/shortlinkmgr/lib/vendor/` for the bundled source and its own license.

## License

MIT — see [LICENSE](LICENSE) for details.

