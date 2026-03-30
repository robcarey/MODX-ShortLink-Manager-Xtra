<?php
/**
 * ShortLink Manager - English default lexicon
 * @package shortlinkmgr
 */

// Menu
$_lang['shortlinkmgr']           = 'ShortLink Manager';
$_lang['shortlinkmgr.menu_desc'] = 'Manage short/vanity URLs with UTM tracking and click analytics.';

// General UI
$_lang['shortlinkmgr.add']              = 'Add Short Link';
$_lang['shortlinkmgr.edit']             = 'Edit Short Link';
$_lang['shortlinkmgr.delete']           = 'Delete Short Link';
$_lang['shortlinkmgr.search']           = 'Search...';
$_lang['shortlinkmgr.refresh']          = 'Refresh';
$_lang['shortlinkmgr.confirm_delete']   = 'Are you sure you want to delete this short link? All associated click logs will also be deleted.';
$_lang['shortlinkmgr.save']             = 'Save';
$_lang['shortlinkmgr.cancel']           = 'Cancel';
$_lang['shortlinkmgr.success_create']   = 'Short link created successfully.';
$_lang['shortlinkmgr.success_update']   = 'Short link updated successfully.';
$_lang['shortlinkmgr.success_delete']   = 'Short link deleted.';
$_lang['shortlinkmgr.err_save']             = 'Unable to save — please check your entries and try again.';
$_lang['shortlinkmgr.err_shortcode_exists'] = 'This shortcode is already taken. Please choose a different one, or leave it blank to auto-generate a new code.';
$_lang['shortlinkmgr.err_shortcode_invalid']  = 'Shortcodes can only contain lowercase letters (a–z), numbers (0–9), hyphens (-), underscores (_), and forward slashes (/). They cannot start or end with a special character.';
$_lang['shortlinkmgr.err_no_target']    = 'Please provide a destination — either select a Resource or enter a Redirect URL.';
$_lang['shortlinkmgr.link_err_ns']      = 'Could not identify the short link to update. Please close this window and try again.';
$_lang['shortlinkmgr.link_err_nf']      = 'This short link no longer exists — it may have been deleted. Please refresh the grid.';
$_lang['shortlinkmgr.link_err_remove']  = 'Something went wrong while deleting this short link. Please try again.';

// Grid columns
$_lang['shortlinkmgr.col_shortcode']     = 'Shortcode';
$_lang['shortlinkmgr.col_title']         = 'Title';
$_lang['shortlinkmgr.col_redirect_id']   = 'Resource ID';
$_lang['shortlinkmgr.col_redirect_url']  = 'Redirect URL';
$_lang['shortlinkmgr.col_redirect_type'] = 'Type';
$_lang['shortlinkmgr.col_published']     = 'Published';
$_lang['shortlinkmgr.col_click_count']   = 'Clicks';
$_lang['shortlinkmgr.col_expires_at']    = 'Expires';
$_lang['shortlinkmgr.col_created_at']    = 'Created';
$_lang['shortlinkmgr.col_actions']       = 'Actions';

// Tab titles
$_lang['shortlinkmgr.tab_basic']    = 'Basic Information';
$_lang['shortlinkmgr.tab_utm']      = 'UTM Parameters';
$_lang['shortlinkmgr.tab_advanced'] = 'Advanced';

// Form fields
$_lang['shortlinkmgr.field_shortcode']         = 'Shortcode';
$_lang['shortlinkmgr.field_shortcode_desc']    = 'Leave blank to auto-generate. Lowercase letters and digits only. Must be unique.';
$_lang['shortlinkmgr.field_title']             = 'Title';
$_lang['shortlinkmgr.field_title_desc']        = 'Internal label for this short link.';
$_lang['shortlinkmgr.field_description']       = 'Description / Notes';
$_lang['shortlinkmgr.field_published']         = 'Published';
$_lang['shortlinkmgr.field_redirect_id']       = 'Resource ID (MODX)';
$_lang['shortlinkmgr.field_redirect_id_desc']  = 'ID of a MODX resource. Takes priority over the URL field. Leave blank if using a manual URL.';
$_lang['shortlinkmgr.field_redirect_url']      = 'Redirect URL';
$_lang['shortlinkmgr.field_redirect_url_desc'] = 'Full URL (https://...) or site-relative path (/page/). Used as fallback if Resource ID is not set or the resource is unpublished.';
$_lang['shortlinkmgr.field_redirect_type']     = 'Redirect Type';
$_lang['shortlinkmgr.field_redirect_type_desc']= '302 Temporary is recommended for marketing/campaign links — it allows changing the destination without the browser caching the old target. Use 301 Permanent only for links that will never change destination.';
$_lang['shortlinkmgr.field_expires_at']        = 'Expiry Date/Time';
$_lang['shortlinkmgr.field_expires_at_desc']   = 'Optional. Leave blank for no expiry. After this date/time the short link will stop redirecting.';

// UTM fields
$_lang['shortlinkmgr.section_utm']        = 'UTM Parameters';
$_lang['shortlinkmgr.field_utm_source']   = 'utm_source';
$_lang['shortlinkmgr.field_utm_medium']   = 'utm_medium';
$_lang['shortlinkmgr.field_utm_campaign'] = 'utm_campaign';
$_lang['shortlinkmgr.field_utm_term']     = 'utm_term';
$_lang['shortlinkmgr.field_utm_content']  = 'utm_content';

// Advanced fields
$_lang['shortlinkmgr.section_advanced']        = 'Advanced';
$_lang['shortlinkmgr.field_anchor']            = 'Anchor (#hash)';
$_lang['shortlinkmgr.field_anchor_desc']       = 'Optional. Do not include the # character. Appended to the end of the final URL.';
$_lang['shortlinkmgr.field_additional_params'] = 'Additional Parameters';
$_lang['shortlinkmgr.field_additional_params_desc'] = 'Extra query string parameters in key=value format, separated by &. Example: ref=newsletter&promo=spring';

// Import
$_lang['shortlinkmgr.import']               = 'Import CSV';
$_lang['shortlinkmgr.import_title']          = 'Import Short Links from CSV';
$_lang['shortlinkmgr.import_file']           = 'CSV File';
$_lang['shortlinkmgr.import_file_desc']      = 'Select a CSV file with a header row. Recognised columns: shortcode, title, description, published, redirect_url, redirect_id, redirect_type, utm_source, utm_medium, utm_campaign, utm_term, utm_content, anchor, additional_params, expires_at.';
$_lang['shortlinkmgr.import_success']        = 'Import complete: %d imported, %d skipped.';
$_lang['shortlinkmgr.import_err_no_file']    = 'No file was uploaded. Please select a CSV file.';
$_lang['shortlinkmgr.import_err_read']       = 'Could not read the uploaded file.';
$_lang['shortlinkmgr.import_err_header']     = 'The CSV file has no valid header row or no recognised columns.';

// Shortcode preview
$_lang['shortlinkmgr.preview_url'] = 'Short URL Preview';

// QR Code tab
$_lang['shortlinkmgr.tab_qrcode']           = 'QR Code';
$_lang['shortlinkmgr.qr_preview']           = 'QR Code Preview';
$_lang['shortlinkmgr.qr_generate']          = 'Generate QR Code';
$_lang['shortlinkmgr.qr_regenerate']        = 'Regenerate';
$_lang['shortlinkmgr.qr_download_svg']      = 'Download SVG';
$_lang['shortlinkmgr.qr_download_png']      = 'Download PNG';
$_lang['shortlinkmgr.qr_encoded_url']       = 'Encoded URL';
$_lang['shortlinkmgr.qr_not_generated']     = 'No QR code has been generated yet. Click "Generate QR Code" to create one.';
$_lang['shortlinkmgr.qr_generating']        = 'Generating QR code...';
$_lang['shortlinkmgr.qr_save_first']        = 'Please save the short link first before generating a QR code.';
$_lang['shortlinkmgr.err_qr_no_id']         = 'No short link ID provided.';
$_lang['shortlinkmgr.err_qr_generate']      = 'QR code generation failed.';
$_lang['shortlinkmgr.err_not_found']        = 'Short link not found.';
