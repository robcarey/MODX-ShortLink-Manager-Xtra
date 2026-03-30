/**
 * ShortLink Manager - Create / Edit window
 */

// ── Shared form fields definition (tabbed layout) ────────────────────────────
// @param {Object} config  Optional. { showQRTab: true } to include the QR Code tab.
Shortlinkmgr.getFormFields = function (config) {
    config = config || {};

    var tabs = [
        // ── Tab 1: Basic Information ──────────────────────────────────
        {
            title:    _('shortlinkmgr.tab_basic'),
            layout:   'form',
            defaults: { anchor: '100%', msgTarget: 'under' },
            items: [
                {
                    xtype:       'textfield',
                    fieldLabel:  _('shortlinkmgr.field_shortcode'),
                    name:        'shortcode',
                    allowBlank:  true,
                    regex:       /^[a-z0-9]([a-z0-9\-\_\/]*[a-z0-9])?$/,
                    regexText:   'Lowercase letters, digits, hyphens, underscores, and forward slashes only.'
                },
                {
                    xtype: 'displayfield', fieldLabel: ' ', labelSeparator: '',
                    value: '<small style="color:#888;">' + _('shortlinkmgr.field_shortcode_desc') + '</small>'
                },
                {
                    xtype:      'textfield',
                    fieldLabel: _('shortlinkmgr.field_title'),
                    name:       'title',
                    allowBlank: true
                },
                {
                    xtype:      'textarea',
                    fieldLabel: _('shortlinkmgr.field_description'),
                    name:       'description',
                    height:     60,
                    allowBlank: true
                },
                {
                    xtype:      'checkbox',
                    fieldLabel: _('shortlinkmgr.field_published'),
                    name:       'published',
                    inputValue: 1,
                    checked:    true
                },
                // ── Redirect Target (inline in Basic tab) ────────────
                {
                    xtype:      'numberfield',
                    fieldLabel: _('shortlinkmgr.field_redirect_id'),
                    name:       'redirect_id',
                    allowBlank: true,
                    allowDecimals: false,
                    allowNegative: false,
                    minValue:   1
                },
                {
                    xtype: 'displayfield', fieldLabel: ' ', labelSeparator: '',
                    value: '<small style="color:#888;">' + _('shortlinkmgr.field_redirect_id_desc') + '</small>'
                },
                {
                    xtype:      'textfield',
                    fieldLabel: _('shortlinkmgr.field_redirect_url'),
                    name:       'redirect_url',
                    allowBlank: true,
                    vtype:      'url'
                },
                {
                    xtype: 'displayfield', fieldLabel: ' ', labelSeparator: '',
                    value: '<small style="color:#888;">' + _('shortlinkmgr.field_redirect_url_desc') + '</small>'
                },
                {
                    xtype:      'radiogroup',
                    fieldLabel: _('shortlinkmgr.field_redirect_type'),
                    name:       'redirect_type',
                    items: [
                        { boxLabel: '302 &mdash; Temporary (recommended)', name: 'redirect_type', inputValue: '302', checked: true },
                        { boxLabel: '301 &mdash; Permanent', name: 'redirect_type', inputValue: '301' }
                    ]
                },
                {
                    xtype: 'displayfield', fieldLabel: ' ', labelSeparator: '',
                    value: '<small style="color:#888;">' + _('shortlinkmgr.field_redirect_type_desc') + '</small>'
                }
            ]
        },
        // ── Tab 2: UTM Parameters ────────────────────────────────────
        {
            title:    _('shortlinkmgr.tab_utm'),
            layout:   'form',
            defaults: { anchor: '100%', msgTarget: 'under' },
            items: [
                { xtype: 'textfield', fieldLabel: _('shortlinkmgr.field_utm_source'),   name: 'utm_source',   allowBlank: true },
                { xtype: 'textfield', fieldLabel: _('shortlinkmgr.field_utm_medium'),   name: 'utm_medium',   allowBlank: true },
                { xtype: 'textfield', fieldLabel: _('shortlinkmgr.field_utm_campaign'), name: 'utm_campaign', allowBlank: true },
                { xtype: 'textfield', fieldLabel: _('shortlinkmgr.field_utm_term'),     name: 'utm_term',     allowBlank: true },
                { xtype: 'textfield', fieldLabel: _('shortlinkmgr.field_utm_content'),  name: 'utm_content',  allowBlank: true }
            ]
        },
        // ── Tab 3: Advanced ──────────────────────────────────────────
        {
            title:    _('shortlinkmgr.tab_advanced'),
            layout:   'form',
            defaults: { anchor: '100%', msgTarget: 'under' },
            items: [
                {
                    xtype:      'textfield',
                    fieldLabel: _('shortlinkmgr.field_anchor'),
                    name:       'anchor',
                    allowBlank: true
                },
                {
                    xtype: 'displayfield', fieldLabel: ' ', labelSeparator: '',
                    value: '<small style="color:#888;">' + _('shortlinkmgr.field_anchor_desc') + '</small>'
                },
                {
                    xtype:      'textarea',
                    fieldLabel: _('shortlinkmgr.field_additional_params'),
                    name:       'additional_params',
                    height:     60,
                    allowBlank: true
                },
                {
                    xtype: 'displayfield', fieldLabel: ' ', labelSeparator: '',
                    value: '<small style="color:#888;">' + _('shortlinkmgr.field_additional_params_desc') + '</small>'
                },
                {
                    xtype:      'datefield',
                    fieldLabel: _('shortlinkmgr.field_expires_at'),
                    name:       'expires_at_date',
                    format:     'Y-m-d',
                    allowBlank: true,
                    anchor:     '100%'
                },
                {
                    xtype:      'timefield',
                    fieldLabel: 'Expiry Time',
                    name:       'expires_at_time',
                    format:     'H:i',
                    increment:  15,
                    allowBlank: true,
                    anchor:     '100%'
                },
                {
                    xtype: 'displayfield', fieldLabel: ' ', labelSeparator: '',
                    value: '<small style="color:#888;">' + _('shortlinkmgr.field_expires_at_desc') + '</small>'
                }
            ]
        }
    ];

    // ── Tab 4: QR Code (edit mode only) ──────────────────────────────
    if (config.showQRTab) {
        tabs.push({
            title:    _('shortlinkmgr.tab_qrcode'),
            itemId:   'slm-qrcode-tab',
            cls:      'slm-qrcode-tab',
            layout:   'anchor',
            defaults: { anchor: '100%' },
            items: [
                {
                    xtype: 'container',
                    itemId: 'slm-qr-toolbar',
                    cls:   'slm-qr-toolbar',
                    html:  '<div class="slm-qr-actions">' +
                               '<button type="button" class="slm-qr-btn slm-qr-btn-generate">' + _('shortlinkmgr.qr_generate') + '</button>' +
                               '<button type="button" class="slm-qr-btn slm-qr-btn-regenerate" style="display:none;">' + _('shortlinkmgr.qr_regenerate') + '</button>' +
                               '<a class="slm-qr-btn slm-qr-btn-download-svg" href="#" download style="display:none;">' + _('shortlinkmgr.qr_download_svg') + '</a>' +
                               '<a class="slm-qr-btn slm-qr-btn-download-png" href="#" download style="display:none;">' + _('shortlinkmgr.qr_download_png') + '</a>' +
                           '</div>'
                },
                {
                    xtype: 'container',
                    itemId: 'slm-qr-info',
                    cls:   'slm-qr-info',
                    html:  ''
                },
                {
                    xtype: 'container',
                    itemId: 'slm-qr-preview',
                    cls:   'slm-qr-preview',
                    html:  '<div class="slm-qr-placeholder">' + _('shortlinkmgr.qr_not_generated') + '</div>'
                }
            ]
        });
    }

    return [
        {
            xtype: 'hidden',
            name:  'id'
        },
        {
            xtype:           'modx-tabs',
            autoHeight:      true,
            deferredRender:  false,   // render all tabs so hidden fields are in the DOM
            border:          false,
            defaults:        { autoHeight: true, bodyStyle: 'padding: 10px;' },
            items:           tabs
        }
    ];
};

// ── Helper: calculate window height as 80% of viewport, min 450px ────────────
Shortlinkmgr.getWindowHeight = function () {
    var vh = Ext.getBody().getViewSize().height || window.innerHeight || 450;
    return Math.max(450, Math.round(vh * 0.8));
};

// ── Create Window ─────────────────────────────────────────────────────────────
Shortlinkmgr.window.CreateLink = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        title:         _('shortlinkmgr.add'),
        url:           Shortlinkmgr.config.connector_url,
        baseParams:    { action: 'links/create', namespace: 'shortlinkmgr' },
        width:         560,
        height:        Shortlinkmgr.getWindowHeight(),
        autoHeight:    false,
        fields:        Shortlinkmgr.getFormFields()
    });
    Shortlinkmgr.window.CreateLink.superclass.constructor.call(this, config);
};
Ext.extend(Shortlinkmgr.window.CreateLink, MODx.Window);
Ext.reg('shortlinkmgr-window-create-link', Shortlinkmgr.window.CreateLink);

// ── Update Window ─────────────────────────────────────────────────────────────
Shortlinkmgr.window.UpdateLink = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        title:         _('shortlinkmgr.edit'),
        url:           Shortlinkmgr.config.connector_url,
        baseParams:    { action: 'links/update', namespace: 'shortlinkmgr' },
        width:         620,
        height:        Shortlinkmgr.getWindowHeight(),
        autoHeight:    false,
        fields:        Shortlinkmgr.getFormFields({ showQRTab: true })
    });
    Shortlinkmgr.window.UpdateLink.superclass.constructor.call(this, config);
};
Ext.extend(Shortlinkmgr.window.UpdateLink, MODx.Window, {
    /**
     * Load a record into the form by ID.
     * Fetches fresh data from the server before populating.
     */
    setValues: function (values) {
        if (!values || !values.id) return;

        var self = this;

        MODx.Ajax.request({
            url:       Shortlinkmgr.config.connector_url,
            params:    { action: 'links/get', namespace: 'shortlinkmgr', id: values.id },
            listeners: {
                success: {
                    fn: function (r) {
                        var form = this.fp.getForm();
                        // radiogroup needs special handling
                        form.setValues(r.object);
                        // Manually set redirect_type radio
                        var rg = form.findField('redirect_type');
                        if (rg && r.object.redirect_type) {
                            rg.setValue(String(r.object.redirect_type));
                        }
                        // published checkbox
                        var cb = form.findField('published');
                        if (cb) cb.setValue(r.object.published == 1);
                        // Split expires_at into date + time fields
                        if (r.object.expires_at) {
                            var parts = r.object.expires_at.split(' ');
                            var df = form.findField('expires_at_date');
                            var tf = form.findField('expires_at_time');
                            if (df) df.setValue(parts[0] || '');
                            if (tf && parts[1]) tf.setValue(parts[1].substring(0, 5));
                        }

                        // Store the link ID for the QR tab and bind QR buttons
                        self._linkId = r.object.id;
                        self._initQRTab();
                    },
                    scope: this
                }
            }
        });
    },

    /**
     * Initialise the QR Code tab: bind button handlers and auto-load if files exist.
     */
    _initQRTab: function () {
        var self = this;
        var el   = this.getEl();
        if (!el) return;

        var dom = el.dom || el;

        // Generate button
        var btnGen = dom.querySelector('.slm-qr-btn-generate');
        if (btnGen) {
            btnGen.onclick = function () { self._generateQR(false); };
        }

        // Regenerate button
        var btnRegen = dom.querySelector('.slm-qr-btn-regenerate');
        if (btnRegen) {
            btnRegen.onclick = function () { self._generateQR(true); };
        }

        // Auto-load: attempt to fetch existing QR code
        this._generateQR(false);
    },

    /**
     * Call the QR processor to generate (or retrieve cached) QR code files.
     */
    _generateQR: function (regenerate) {
        var self = this;
        if (!this._linkId) return;

        var dom = (this.getEl().dom || this.getEl());
        var preview = dom.querySelector('.slm-qr-preview');
        var info    = dom.querySelector('.slm-qr-info');
        if (preview) {
            preview.innerHTML = '<div class="slm-qr-placeholder">' + _('shortlinkmgr.qr_generating') + '</div>';
        }

        MODx.Ajax.request({
            url:    Shortlinkmgr.config.connector_url,
            params: {
                action:     'links/qrcode',
                namespace:  'shortlinkmgr',
                id:         this._linkId,
                regenerate: regenerate ? 1 : 0
            },
            listeners: {
                success: {
                    fn: function (r) {
                        self._showQRResult(r.object || r);
                    },
                    scope: this
                },
                failure: {
                    fn: function (r) {
                        if (preview) {
                            preview.innerHTML = '<div class="slm-qr-placeholder slm-qr-error">' +
                                (r.message || _('shortlinkmgr.err_qr_generate')) + '</div>';
                        }
                    },
                    scope: this
                }
            }
        });
    },

    /**
     * Display the generated QR code in the tab.
     */
    _showQRResult: function (data) {
        var dom = (this.getEl().dom || this.getEl());

        // Preview
        var preview = dom.querySelector('.slm-qr-preview');
        if (preview && data.svg_content) {
            preview.innerHTML = '<div class="slm-qr-image">' + data.svg_content + '</div>';
        }

        // Info line
        var info = dom.querySelector('.slm-qr-info');
        if (info && data.short_url) {
            info.innerHTML = '<div class="slm-qr-encoded-url">' +
                '<strong>' + _('shortlinkmgr.qr_encoded_url') + ':</strong> ' +
                '<code>' + data.short_url + '</code></div>';
        }

        // Show regenerate, download buttons — hide generate
        var btnGen   = dom.querySelector('.slm-qr-btn-generate');
        var btnRegen = dom.querySelector('.slm-qr-btn-regenerate');
        var btnSvg   = dom.querySelector('.slm-qr-btn-download-svg');
        var btnPng   = dom.querySelector('.slm-qr-btn-download-png');

        if (btnGen)   btnGen.style.display   = 'none';
        if (btnRegen) btnRegen.style.display  = '';
        if (btnSvg && data.svg_url) {
            btnSvg.style.display = '';
            btnSvg.href = data.svg_url;
        }
        if (btnPng && data.png_url) {
            btnPng.style.display = '';
            btnPng.href = data.png_url;
        }
    }
});
Ext.reg('shortlinkmgr-window-update-link', Shortlinkmgr.window.UpdateLink);

// ── Import CSV Window ────────────────────────────────────────────────────────
Shortlinkmgr.window.ImportCSV = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        title:      _('shortlinkmgr.import_title'),
        url:        Shortlinkmgr.config.connector_url,
        baseParams: { action: 'links/import', namespace: 'shortlinkmgr' },
        fileUpload: true,
        width:      520,
        autoHeight: true,
        fields: [
            {
                xtype:      'fileuploadfield',
                fieldLabel: _('shortlinkmgr.import_file'),
                name:       'file',
                allowBlank: false,
                anchor:     '100%',
                buttonText: '...'
            },
            {
                xtype: 'displayfield', fieldLabel: ' ', labelSeparator: '',
                value: '<small style="color:#888;">' + _('shortlinkmgr.import_file_desc') + '</small>'
            }
        ]
    });
    Shortlinkmgr.window.ImportCSV.superclass.constructor.call(this, config);
};
Ext.extend(Shortlinkmgr.window.ImportCSV, MODx.Window);
Ext.reg('shortlinkmgr-window-import-csv', Shortlinkmgr.window.ImportCSV);
