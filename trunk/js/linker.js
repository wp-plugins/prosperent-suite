(function() {
    tinymce.create('tinymce.plugins.linkerPlugin', {
 
        init : function(ed, url){
			ed.addCommand('auto-linker', function() {
				ed.windowManager.open({
					file : url + '/../autolink.html',
					width : 300 + parseInt(ed.getLang('linker.delta_width', 0)),
					height : 490 + parseInt(ed.getLang('linker.delta_height', 0)),
					inline : 1
				}, {
					plugin_url : url
				});
			});
			ed.addButton('linker', {title : 'Auto-Link', cmd : 'auto-linker', image: url + "/../img/prosp-link-icon.png" });
        }
    });
 
    tinymce.PluginManager.add('linker', tinymce.plugins.linkerPlugin);
})();