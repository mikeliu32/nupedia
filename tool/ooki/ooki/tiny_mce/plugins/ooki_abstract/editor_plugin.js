var ooki_abstractObjId = 'ookiAbstractTM';

(function() {
	tinymce.create('tinymce.plugins.OokiAbstract', {
		getInfo : function() {
			return {
				longname : 'Abstract plugin',
				author : 'Monkia',
				authorurl : 'http://ookon.nuweb.cc',
				infourl : 'http://ookon.nuweb.cc',
				version : '2.0'
			};
		},
	
		init : function(ed, url) {
			var t = this, dom = tinymce.DOM;
			t.editor = ed;
			t.m_url = url;
			ed.ooki_abstract_bShowTreeView = true;
			
//			B_LoadScrip( t.m_url + '/abstractTM.js' );
			
			ed.onSetContent.add(function(ed, o) {
				t._ViewToEdit(ed.getBody());
			});
			
			ed.onPostProcess.add(function(ed, o) {
				if ( ed.ooki_abstract_bShowTreeView ) {
					o.content = t._EditToView(o.content);
				}
			});
			
		},
		
		createControl : function(n, cm) {
			switch (n) {
				case "abstract":
					var t = this, ed = t.editor;
					
					
					var btn = cm.createSplitButton('abstract', { 
						title : 'ooki_abstract.desc', 
						image : t.m_url + '/images/abstract.gif', 
						onclick : function() {
							var selHtml = ed.selection.getContent({no_events: true});
							if ( selHtml != "" || t._isObj(ed.selection.getNode()) ) {
								ed.windowManager.open({
									file : t.m_url + '/abstract.html',
									width : 400,
									height : 150,
									scrollbars : false,
									inline : 1
								}, {
									plugin_url : t.m_url,
									selData : selHtml
								});
							}
						} 
					}); 
					
					btn.onRenderMenu.add(function(btn, menu) {
						menu.onShowMenu.add(function() {
							ed.windowManager.open({
								file : t.m_url + '/abstract_adv.html',
								width : 550,
								height : 500,
								scrollbars : false,
								inline : 1
							}, {
								plugin_url : t.m_url
							});
						});
						
						menu.add({id : t.editor.dom.uniqueId(), title : 'ooki_abstract.Adjust', 'class' : 'mceMenuItemTitle'}).setDisabled(1);
						
					});
						
					// co => not sel, n => Obj, 
					ed.onNodeChange.add(function(ed, cm, n, co) {
						cm.setDisabled('abstract', false);
					});
					
					return btn;
			}
		},
		
		_isObj : function(n) {
			while ( n && n.className != ooki_abstractObjId )
				n = n.parentNode;
			return n;
		},
		
		_EditToView : function(c) {
			var t = this, ed = t.editor;
			var data;
			
			data = abstract_getLevelArray(ed.getDoc());
			return abstract_Array2Html(data, ed.getLang('ooki_abstract.Dir')) + c;
		},
		
		_ViewToEdit : function(p) {
			var t = this, ed = t.editor, bTreeView, tv;
			
			tv = $(p).find('#'+abstractTM_TableID);
			bTreeView = tv.length > 0;
			tv.remove();
			ed.ooki_abstract_bShowTreeView = (!bTreeView && abstract_getLevelIds(p,0,0).length ? false : true);
		}
		
	});

	
	// Register plugin
	tinymce.PluginManager.add('ooki_abstract', tinymce.plugins.OokiAbstract);
})();