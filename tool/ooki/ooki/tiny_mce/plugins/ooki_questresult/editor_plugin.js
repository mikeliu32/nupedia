var ooki_questresultObjId = 'ooki_questresultTM';

(function() {
	tinymce.create('tinymce.plugins.OokiQuestResult', {
		getInfo : function() {
			return {
				longname : 'QuestResult plugin',
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
			ed.addButton('questresult', {
				title : 'ooki_questresult.desc',
				image : url+'/images/questresult.gif',
				onclick : function() 
				{
					var oArg, U, oWin;
					oArg= new Object();
					oArg.language = tinyMCE.settings['language'];
					oArg.BaseUrl = url;
					//oArg.ei = editor_id;
					U = url + '/questResult.php';
					oWin = window.open(U, new Date().getTime(), 'height=600px, width=690px, resizable=yes, scrollbars=no, center=yes');
					oWin.focus();
					oWin.dialogArguments = oArg;
					window.dialogArguments = oArg;
				}
			});

			//ed.addShortcut('ctrl+k', 'advlink.advlink_desc', 'mceAdvLink');

			// co => not sel, n => Obj, 
			ed.onNodeChange.add(function(ed, cm, n, co, o) {
				cm.setDisabled('questresult', false);
			});
		}
	});

	// Register plugin
	tinymce.PluginManager.add('ooki_questresult', tinymce.plugins.OokiQuestResult);
})();