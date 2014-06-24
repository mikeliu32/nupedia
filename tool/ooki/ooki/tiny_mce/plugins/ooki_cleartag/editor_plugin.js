(function() {
	var each = tinymce.each;

	tinymce.create('tinymce.plugins.ClearTag', {
		getInfo : function() {
			return {
				longname : 'Clear Tag plugin',
				author : 'Monkia',
				authorurl : 'http://ookon.nuweb.cc',
				infourl : 'http://ookon.nuweb.cc',
				version : '2.0'
			};
		},
	
		init : function(ed, url) {
			var t = this, DOM = tinymce.DOM;
			
			t.editor = ed;
			t.m_url = url;

			// Register buttons
			ed.addButton('cleartag', {
				title : 'ooki_cleartag.desc',
				image : url+'/images/cleartag.gif',
				onclick : function() 
				{
					var se = ed.selection;
					var focusElm = se.getNode();
					var selContent = se.getContent({no_events: true});
					if (selContent.length) {
						var selContentText = se.getContent({no_events: true, format : 'text'});
						var check = confirm(ed.getLang("ooki_cleartag.is_clear"));
						if(check) {
							tinyMCE.execCommand("mceInsertContent", false, selContentText.replace(/\r\n/ig, "<br>"));
						}
					}
				}
			});

			// co => not sel, n => Obj, 
			ed.onNodeChange.add(function(ed, cm, n, co) {
				if (co)
					cm.setDisabled('cleartag', true);
				else
					cm.setDisabled('cleartag', false);
			});
			
		}
		
	});

	// Register plugin
	tinymce.PluginManager.add('ooki_cleartag', tinymce.plugins.ClearTag);
})();

