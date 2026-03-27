/**
 * ShortLink Manager - Home Panel
 * Contains the links grid and a short URL info bar.
 */
Shortlinkmgr.panel.Home = function (config) {
    config = config || {};

    var prefix   = Shortlinkmgr.config.path_prefix || 'go';
    var baseUrl  = (Shortlinkmgr.config.base_url || '').replace(/\/$/, '');
    var infoHtml = '<div class="shortlinkmgr-info-bar">'
        + '<strong>Short URL base:</strong> '
        + '<code>' + Ext.util.Format.htmlEncode(baseUrl + '/' + prefix + '/') + '{shortcode}</code>'
        + ' &nbsp;&mdash;&nbsp; <em>Double-click a row to edit. Click a shortcode to open the link.</em>'
        + '</div>';

    Ext.applyIf(config, {
        id:       'shortlinkmgr-panel-home',
        cls:      'container',
        items: [
            {
                html:   infoHtml,
                border: false,
                cls:    'shortlinkmgr-info-panel'
            },
            {
                xtype: 'shortlinkmgr-grid-links',
                cls:   'main-wrapper'
            }
        ]
    });

    Shortlinkmgr.panel.Home.superclass.constructor.call(this, config);
};
Ext.extend(Shortlinkmgr.panel.Home, MODx.Panel);
Ext.reg('shortlinkmgr-panel-home', Shortlinkmgr.panel.Home);
