var ooki_questObjId = 'ooki_questTM';

(function() {
	tinymce.create('tinymce.plugins.OokiQuest', {
		getInfo : function() {
			return {
				longname : 'Quest plugin',
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
			ed.addButton('quest', {
				title : 'ooki_quest.desc',
				image : url+'/images/quest.gif',
				onclick : function() 
				{
					ed.windowManager.open({
						file : url + '/quest.html',
						width : 450,
						height : 400,
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
				if (!co || n.nodeName == "A")
					cm.setDisabled('quest', false);
				else
					cm.setDisabled('quest', true);
			});
		}
	});

	// Register plugin
	tinymce.PluginManager.add('ooki_quest', tinymce.plugins.OokiQuest);
})();