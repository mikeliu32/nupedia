var ooki_questObjId = 'ooki_youtubeTM';

(function() {
	tinymce.create('tinymce.plugins.YouTube', {
		getInfo : function() {
			return {
				longname : 'YouTube plugin',
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
			ed.addButton('youtube', {
				title : 'ooki_youtube.desc',
				image : url+'/images/youtube.gif',
				onclick : function() 
				{
					var se = ed.selection;
					var focusElm = se.getNode();
					var data = (focusElm.className == "mceItemYouTube" ? focusElm.data : null);
				
					ed.windowManager.open({
						file : url + '/youtube.html',
						width : 450,
						height : 300,
						scrollbars : false,
						inline : 1
					}, {
						plugin_url : url,
						plugin_data : data
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
	tinymce.PluginManager.add('ooki_youtube', tinymce.plugins.YouTube);
})();