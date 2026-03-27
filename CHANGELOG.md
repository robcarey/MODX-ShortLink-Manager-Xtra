# Changelog

All notable changes to ShortLink Manager will be documented in this file.

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

