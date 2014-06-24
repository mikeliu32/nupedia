var ooki_internalsearchObjId = 'ooki_internalsearchTM';

(function() {
	tinymce.create('tinymce.plugins.OokiInternalSearch', {
		getInfo : function() {
			return {
				longname : 'InternalSearch plugin',
				author : 'Monkia',
				authorurl : 'http://ookon.nuweb.cc',
				infourl : 'http://ookon.nuweb.cc',
				version : '2.0'
			};
		},
	
		init : function(ed, url) {
			var t = this;
			t.editor = ed;

			// Register commands
			//ed.addCommand('mceAudio', function() {
			//});

			// Register buttons
			ed.addButton('internalsearch', {
				title : 'ooki_internalsearch.desc',
				image : url+'/images/internalSearch.gif',
				onclick : function() 
				{
					var n = ed.selection.getNode();
					var selText = ed.selection.getContent({format:'text'});
					if (selText != "" || (n && n.nodeName == 'A')) 
					{
						if (n && n.nodeName == "A") {
							selText = n.title;
							if (selText == "")
								selText = n.innerText;
						}
						
						var U = url + '/internalsearch.html';
						ed.windowManager.open({
							file : U,
							width : 550,
							height : 500,
							scrollbars : false,
							inline : 1
						}, {
							plugin_url : url
						});
					}				
				}
			});

			//ed.addShortcut('ctrl+k', 'advlink.advlink_desc', 'mceAdvLink');

			// co => not sel, n => Obj, 
			ed.onNodeChange.add(function(ed, cm, n, co) {
				if ( !co || n.nodeName == "A" )
					cm.setDisabled('internalsearch', false);
				else
					cm.setDisabled('internalsearch', true);
			});
		}
	});

	// Register plugin
	tinymce.PluginManager.add('ooki_internalsearch', tinymce.plugins.OokiInternalSearch);
})();