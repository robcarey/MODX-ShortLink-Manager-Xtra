/**
 * ShortLink Manager - JS bootstrap
 * Defines the Shortlinkmgr namespace and shared config.
 * All other widget files extend this namespace.
 */
var Shortlinkmgr = function (config) {
    config = config || {};
    Shortlinkmgr.superclass.constructor.call(this, config);
};
Ext.extend(Shortlinkmgr, Ext.Component, {
    page:    {},
    window:  {},
    grid:    {},
    tree:    {},
    panel:   {},
    combo:   {},
    config:  {}
});
Shortlinkmgr = new Shortlinkmgr();
Shortlinkmgr.config = Shortlinkmgr.config || {};
