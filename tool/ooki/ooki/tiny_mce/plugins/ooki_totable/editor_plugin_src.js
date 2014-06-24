var ooki_totableObjId = 'ooki_totableTM';

(function() {
	tinymce.create('tinymce.plugins.OokiToTable', {
		getInfo : function() {
			return {
				longname : 'ToTable plugin',
				author : 'Monkia',
				authorurl : 'http://ookon.nuweb.cc',
				infourl : 'http://ookon.nuweb.cc',
				version : '2.0'
			};
		},
	
		init : function(ed, url) {
			var t = this;
			t.editor = ed;
			t.m_url = url;
		},
		
		createControl : function(n, cf) {
			switch (n) {
				case "totable":
					var c, t = this, ed = t.editor;
					c = ed.controlManager.createButton('totable', {
						title : 'ooki_totable.desc',
						image : t.m_url + '/images/totable.gif',
						onclick : function() 
						{
							var focusElm = ed.selection.getNode();
							var selHTML = ed.selection.getContent();
							if (selHTML != "") 
							{
								var U = t.m_url + '/totable.html';
								ed.windowManager.open({
									file : U,
									width : 450,
									height : 300,
									scrollbars : false,
									inline : 1
								}, {
									plugin_url : t.m_url
								});
							}				
						}
					});
					
					// co => not sel, n => Obj, 
					ed.onNodeChange.add(function(ed, cm, n, co) {
						if (!co || t._isObj(n))
							cm.setDisabled('totable', false);
						else
							cm.setDisabled('totable', true);
					});
					 
				return c;
			}
		},
		
		_isObj: function(o) {
			return o && o.nodeName == "SPAN" && o.className == ooki_hidecontentObjId;
		}

	});

	// Register plugin
	tinymce.PluginManager.add('ooki_totable', tinymce.plugins.OokiToTable);
})();