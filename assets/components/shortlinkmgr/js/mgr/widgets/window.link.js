/**
 * ShortLink Manager - Create / Edit window
 */

// ── Shared form fields definition (tabbed layout) ────────────────────────────
Shortlinkmgr.getFormFields = function () {
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
            items: [
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
            ]
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
        width:         560,
        height:        Shortlinkmgr.getWindowHeight(),
        autoHeight:    false,
        fields:        Shortlinkmgr.getFormFields()
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
                    },
                    scope: this
                }
            }
        });
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
