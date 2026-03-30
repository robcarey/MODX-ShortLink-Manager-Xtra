# Changelog

All notable changes to ShortLink Manager will be documented in this file.

## [1.0.2-pl] — 2026-03-30

### Added — QR Code Generation

- **QR Code tab** on the Edit Short Link window — generates branded QR codes from the short link's full URL
- QR codes are output as **SVG** (true vector, ideal for print) and **PNG** (raster), downloadable directly from the manager
- **Customisable colours** via system settings:
  - Data pattern colour (the small squares)
  - Finder pattern outer border colour (the three large corner squares)
  - Finder pattern inner eye colour (the centre of each finder square)
  - Background colour (or transparent)
- **Centre logo embedding** — point a system setting at a square SVG file and it is automatically centred in the QR code at the maximum safe size; error correction is raised to H (30% recovery) when a logo is present
- **7 new system settings** in the "QR Code" area: filename prefix, image size, background colour, pattern colour, finder border colour, finder eye colour, and logo file path — all with detailed descriptions
- Generated QR files are **cached** as `{prefix}-{id}.svg/.png` in `assets/components/shortlinkmgr/qr-codes/`; click **Regenerate** to rebuild after changing settings
- QR files are **automatically deleted** when a short link is removed
- QR Code tab is **only visible when editing** an existing link (not on create), since the full URL must exist before a code can be generated
- Bundled [chillerlan/php-qrcode](https://github.com/chillerlan/php-qrcode) v4.4.2 (MIT) for QR encoding — no external dependencies required
- PNG generation requires ext-gd; if not available, SVG still generates and the PNG download button is hidden gracefully

### Changed

- Edit window width increased from 560px to 620px to accommodate the QR Code tab content
- PHP minimum version raised to 8.1+ (required by bundled QR library dependency)

---

## [1.0.0-pl] — 2026-03-27

### Initial Release

**Short Link Management**
- Create, edit, and delete short links from a dedicated Custom Manager Page (CMP)
- Auto-generate unique shortcodes (lowercase alphanumeric) or specify your own
- Configurable shortcode length with automatic collision avoidance
- Publish/unpublish toggle with visual grid indicator
- Double-click rows to edit; inline action buttons for edit and delete

**Redirect Targets**
- Redirect to a MODX resource by ID (resolves full URL automatically)
- Redirect to any external URL as a fallback
- Supports 301 (Permanent) and 302 (Temporary) redirect types

**UTM & Campaign Tracking**
- Built-in fields for all five Google Analytics UTM parameters:
  `utm_source`, `utm_medium`, `utm_campaign`, `utm_term`, `utm_content`
- Additional custom query parameters via free-text field
- URL anchor/fragment support (#section)

**Click Analytics**
- Tracks every click with timestamp, IP address, referrer, and user agent
- Running click counter displayed in the grid
- Detailed click log stored in a dedicated database table

**Link Expiry**
- Optional expiry date and time per link (calendar + time picker)
- Expired links are automatically unpublished:
  - On visitor access (plugin auto-unpublishes and serves 404)
  - On CMP grid load (bulk scan unpublishes all expired links)

**Redirect Methods**
- **Plugin mode** (default) — No server config needed; uses MODX `OnPageNotFound` event
- **htaccess mode** — Optional Apache rewrite rules for URL pre-validation

**Installation & Configuration**
- Installs via standard MODX transport package
- System settings for path prefix, redirect method, shortcode length, and table cleanup
- Safe uninstall — database tables preserved by default (configurable)
- Tabbed edit/create windows with responsive height

**Technical**
- Compatible with MODX 2.8+ on PHP 8.x
- xPDO model with proper schema for links and clicks tables
- Paginated, sortable, searchable grid with server-side filtering
- Full English lexicon for all UI strings and setting descriptions

