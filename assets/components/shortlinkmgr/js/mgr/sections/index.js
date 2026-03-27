/**
 * ShortLink Manager - Page section
 * Wires the home panel to the MODX manager page container.
 * Loaded last (via addLastJavascript).
 */
Shortlinkmgr.page.Home = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        components: [
            {
                xtype:  'shortlinkmgr-panel-home',
                renderTo: 'shortlinkmgr-panel-home-div'
            }
        ]
    });
    Shortlinkmgr.page.Home.superclass.constructor.call(this, config);
};
Ext.extend(Shortlinkmgr.page.Home, MODx.Component);
Ext.reg('shortlinkmgr-page-home', Shortlinkmgr.page.Home);

Ext.onReady(function () {
    // Store grid instance globally so renderer action links can call it
    Shortlinkmgr.grid.LinksInstance = null;

    MODx.load({
        xtype:    'shortlinkmgr-page-home',
        listeners: {
            afterrender: {
                fn: function () {
                    Shortlinkmgr.grid.LinksInstance = Ext.getCmp('shortlinkmgr-grid-links');
                },
                single: true
            }
        }
    });
});
