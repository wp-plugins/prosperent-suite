(function() {
    tinymce.create('tinymce.plugins.comparePlugin', {
 
        init : function(ed, url){
			ed.addCommand('auto-compare', function() {
				ed.windowManager.open({
					file : url + '/../autocompare.html',
					width : 300 + parseInt(ed.getLang('compare.delta_width', 0)),
					height : 525 + parseInt(ed.getLang('compare.delta_height', 0)),
					inline : 1
				}, {
					plugin_url : url
				});
			});
			ed.addButton('compare', {title : 'Auto-Compare', cmd : 'auto-compare', image: url + "/../img/prosp-compare-icon.png" });
        }
    });
 
    tinymce.PluginManager.add('compare', tinymce.plugins.comparePlugin);
})();