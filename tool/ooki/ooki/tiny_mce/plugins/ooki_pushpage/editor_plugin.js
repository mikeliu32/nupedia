var ooki_questObjId = 'ooki_PusgPageTM';

(function() {
	tinymce.create('tinymce.plugins.PushPage', {
		getInfo : function() {
			return {
				longname : 'PushPage plugin',
				author : 'Monkia',
				authorurl : 'http://ookon.nuweb.cc',
				infourl : 'http://ookon.nuweb.cc',
				version : '2.0'
			};
		},
	
		init : function(ed, url) {
			var t = this;
			t.editor = ed;

			// Register buttons
			ed.addButton('pushpage', {
				title : 'ooki_pushpage.desc',
				image : url+'/images/pushpage.gif',
				onclick : function() 
				{
					ed.windowManager.open({
						file : url + '/pushpage.html',
						width : 450,
						height : 300,
						scrollbars : false,
						inline : 1
					}, {
						plugin_url : url
					});
				}
			});

			//ed.addShortcut('ctrl+k', 'advlink.advlink_desc', 'mceAdvLink');
			
			// co => not sel, n => Obj, 
			ed.onNodeChange.add(function(ed, cm, n, co, o) {
			});
		}
		
	});

	// Register plugin
	tinymce.PluginManager.add('ooki_pushpage', tinymce.plugins.PushPage);
})();