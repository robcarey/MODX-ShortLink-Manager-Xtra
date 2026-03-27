/**
 * ShortLink Manager - Links Grid
 */
Shortlinkmgr.grid.Links = function (config) {
    config = config || {};

    this.columns = [
        {
            header:    _('shortlinkmgr.col_shortcode'),
            dataIndex: 'shortcode',
            width:     100,
            renderer:  this.renderShortcode.createDelegate(this),
            sortable:  true
        },
        {
            header:    _('shortlinkmgr.col_title'),
            dataIndex: 'title',
            width:     160,
            sortable:  true
        },
        {
            header:    'Target',
            dataIndex: 'redirect_resource_title',
            width:     160,
            renderer:  this.renderTarget.createDelegate(this),
            sortable:  false
        },
        {
            header:    '<div style="text-align:center">' + _('shortlinkmgr.col_redirect_type') + '</div>',
            dataIndex: 'redirect_type',
            width:     55,
            renderer:  this.renderType,
            sortable:  true,
            css:       'text-align:right;'
        },
        {
            header:    '<div style="text-align:center">' + _('shortlinkmgr.col_published') + '</div>',
            dataIndex: 'published',
            width:     65,
            renderer:  this.renderPublished,
            sortable:  true,
            css:       'text-align:right;'
        },
        {
            header:    '<div style="text-align:center">' + _('shortlinkmgr.col_click_count') + '</div>',
            dataIndex: 'click_count',
            width:     55,
            sortable:  true,
            css:       'text-align:right;'
        },
        {
            header:    '<div style="text-align:center">' + _('shortlinkmgr.col_expires_at') + '</div>',
            dataIndex: 'expires_at_display',
            width:     110,
            renderer:  this.renderExpiry.createDelegate(this),
            sortable:  true,
            css:       'text-align:right;'
        },
        {
            header:    '<div style="text-align:center">' + _('shortlinkmgr.col_actions') + '</div>',
            dataIndex: 'id',
            width:     80,
            renderer:  this.renderActions.createDelegate(this),
            sortable:  false,
            css:       'text-align:right;'
        }
    ];

    this.tbar = [
        {
            text:    '<i class="icon icon-plus"></i> ' + _('shortlinkmgr.add'),
            handler: this.createLink,
            scope:   this
        },
        '-',
        {
            xtype:       'textfield',
            name:        'search',
            emptyText:   _('shortlinkmgr.search'),
            width:        220,
            listeners:   {
                'change':     { fn: this.search, scope: this },
                'render':     { fn: function (f) { this.searchField = f; }, scope: this }
            }
        },
        {
            text:    '<i class="icon icon-search"></i>',
            handler: function () { this.search(this.searchField); },
            scope:   this
        },
        '-',
        {
            text:    '<i class="icon icon-refresh"></i> ' + _('shortlinkmgr.refresh'),
            handler: this.refresh,
            scope:   this
        },
        '-',
        {
            text:    '<i class="icon icon-upload"></i> ' + _('shortlinkmgr.import'),
            handler: this.importCSV,
            scope:   this
        }
    ];

    Ext.applyIf(config, {
        id:            'shortlinkmgr-grid-links',
        title:         _('shortlinkmgr'),
        url:           Shortlinkmgr.config.connector_url,
        baseParams:    { action: 'links/getlist', namespace: 'shortlinkmgr' },
        fields:        [
            'id', 'shortcode', 'title', 'description',
            'published', 'redirect_id', 'redirect_url', 'redirect_type',
            'redirect_resource_title',
            'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content',
            'anchor', 'additional_params',
            'click_count', 'expires_at', 'expires_at_display', 'is_expired',
            'created_by', 'created_at', 'updated_at',
            'short_url'
        ],
        columns:       this.columns,
        tbar:          this.tbar,
        viewConfig:    { forceFit: true, enableRowBody: true },
        sm:            new Ext.grid.RowSelectionModel({ singleSelect: true }),
        paging:        true,
        pageSize:      25,
        remoteSort:    true,
        listeners:     {
            rowdblclick: { fn: this.onRowDblClick, scope: this }
        }
    });

    Shortlinkmgr.grid.Links.superclass.constructor.call(this, config);
};

Ext.extend(Shortlinkmgr.grid.Links, MODx.grid.Grid, {

    // ── Renderers ────────────────────────────────────────────────────────────

    renderShortcode: function (val, meta, record) {
        var url = record.data.short_url;
        return '<a href="' + Ext.util.Format.htmlEncode(url) + '" target="_blank" title="' + Ext.util.Format.htmlEncode(url) + '">'
            + Ext.util.Format.htmlEncode(val) + '</a>';
    },

    renderTarget: function (val, meta, record) {
        var d = record.data;
        if (d.redirect_id) {
            var title = d.redirect_resource_title || ('Resource #' + d.redirect_id);
            return '<span title="Resource ID: ' + d.redirect_id + '">[' + d.redirect_id + '] ' + Ext.util.Format.htmlEncode(title) + '</span>';
        }
        if (d.redirect_url) {
            var u = d.redirect_url;
            return '<span title="' + Ext.util.Format.htmlEncode(u) + '">' + Ext.util.Format.htmlEncode(u.length > 40 ? u.substring(0, 40) + '…' : u) + '</span>';
        }
        return '<em style="color:#999;">—</em>';
    },

    renderType: function (val) {
        var color = (val == 301) ? '#c0392b' : '#2980b9';
        return '<span style="color:' + color + ';font-weight:bold;">' + val + '</span>';
    },

    renderPublished: function (val, meta, record) {
        var checked = val ? ' checked' : '';
        return '<label class="slm-toggle" onclick="Ext.getCmp(\'shortlinkmgr-grid-links\').togglePublished(' + record.data.id + '); return false;">'
            + '<input type="checkbox"' + checked + ' />'
            + '<span class="slm-toggle-slider"></span>'
            + '</label>';
    },

    renderExpiry: function (val, meta, record) {
        if (record.data.is_expired) {
            return '<span style="color:#c0392b;" title="Expired">' + Ext.util.Format.htmlEncode(val) + ' &#9888;</span>';
        }
        return Ext.util.Format.htmlEncode(val);
    },

    renderActions: function (val, meta, record) {
        return '<button type="button" class="slm-action-btn slm-action-edit" title="' + _('shortlinkmgr.edit') + '" onclick="Ext.getCmp(\'shortlinkmgr-grid-links\').editLink(' + record.data.id + ');"><i class="icon icon-pencil"></i></button> '
             + '<button type="button" class="slm-action-btn slm-action-delete" title="' + _('shortlinkmgr.delete') + '" onclick="Ext.getCmp(\'shortlinkmgr-grid-links\').confirmDelete(' + record.data.id + ');"><i class="icon icon-trash-o"></i></button>';
    },

    // ── Actions ───────────────────────────────────────────────────────────────

    createLink: function () {
        var win = new Shortlinkmgr.window.CreateLink({
            listeners: {
                success: { fn: this.refresh, scope: this }
            }
        });
        win.show();
    },

    editLink: function (id) {
        var win = new Shortlinkmgr.window.UpdateLink({
            record:    { data: { id: id } },
            listeners: {
                success: { fn: this.refresh, scope: this }
            }
        });
        win.show();
        win.setValues({ id: id });
    },

    togglePublished: function (id) {
        MODx.Ajax.request({
            url:    Shortlinkmgr.config.connector_url,
            params: { action: 'links/togglepublished', namespace: 'shortlinkmgr', id: id },
            listeners: {
                success: { fn: this.refresh, scope: this }
            }
        });
    },

    confirmDelete: function (id) {
        MODx.msg.confirm({
            title:   _('shortlinkmgr.delete'),
            text:    _('shortlinkmgr.confirm_delete'),
            url:     Shortlinkmgr.config.connector_url,
            params:  { action: 'links/remove', namespace: 'shortlinkmgr', id: id },
            listeners: {
                success: { fn: this.refresh, scope: this }
            }
        });
    },

    onRowDblClick: function (grid, rowIndex) {
        var record = grid.getStore().getAt(rowIndex);
        if (record) {
            this.editLink(record.data.id);
        }
    },

    search: function (field) {
        var val = field && field.getValue ? field.getValue() : '';
        this.getStore().baseParams.search = val;
        this.getBottomToolbar().changePage(1);
        this.refresh();
    },

    importCSV: function () {
        var win = new Shortlinkmgr.window.ImportCSV({
            listeners: {
                success: { fn: this.refresh, scope: this }
            }
        });
        win.show();
    },

    refresh: function () {
        this.getStore().reload();
    }
});
Ext.reg('shortlinkmgr-grid-links', Shortlinkmgr.grid.Links);
