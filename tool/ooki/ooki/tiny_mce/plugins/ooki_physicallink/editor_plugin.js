var ooki_physicallinkObjId = 'ooki_physicallinkTM';

(function() {
	tinymce.create('tinymce.plugins.OokiPhysicalLink', {
		getInfo : function() {
			return {
				longname : 'PhysicalLink plugin',
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
			ed.addButton('physicallink', {
				title : 'ooki_physicallink.desc',
				image : url+'/images/physicallink.gif',
				onclick : function() 
				{
					var focusElm = ed.dom.getParent(ed.selection.getNode(), "A");
					var selText = ed.selection.getContent({format:'text'});
					if ( focusElm || selText != "" ) {
						ed.windowManager.open({
							file : url + '/physicallink.html',
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
					cm.setDisabled('physicallink', false);
				else
					cm.setDisabled('physicallink', true);
			});
		}
	});

	// Register plugin
	tinymce.PluginManager.add('ooki_physicallink', tinymce.plugins.OokiPhysicalLink);
})();