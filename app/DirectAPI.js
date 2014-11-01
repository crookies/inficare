Ext.define('inficare.DirectAPI', {
    requires: ['Ext.direct.*']
}, function() {
    var Loader = Ext.Loader,
        wasLoading = Loader.isLoading;
    Loader.loadScriptFile('php/api.php', Ext.emptyFn, Ext.emptyFn, null, true);
    Loader.isLoading = wasLoading;
    Ext.direct.Manager.addProvider(Ext.app.REMOTING_API);
});