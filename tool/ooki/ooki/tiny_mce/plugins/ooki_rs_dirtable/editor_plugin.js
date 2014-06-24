var ooki_rs_dirtableObjId = 'ookiRSDirTableTM';

(function() {

	// 只能在 NUBraim 上面使用
	if ( !ooki_bCXHtmlView ) 
		return;

		
	tinymce.create('tinymce.plugins.OokiRSDirTable', {
		getInfo : function() {
			return {
				longname : 'RS Dir Table plugin',
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

			
			ed.onBeforeSetContent.add(t._ViewToEdit, t);
			
			// ed.onSetContent.add(function(ed, o) {
				// o.content = t._ViewToEdit(o.content);
				// return o.content;
			// });
			
			ed.onPostProcess.add(function(ed, o) {
				o.content = t._EditToView(o.content);
			});
		},
		
		createControl : function(n, cm) {
			switch (n) {
				case "rs_dirtable":
					var c, t = this, ed = t.editor;
					c = ed.controlManager.createButton('rs_dirtable', {
						title : 'ooki_rs_dirtable.desc',
						image : t.m_url+'/images/rs_dirtable.gif',
						onclick : function() {
							var data = external.AE_B_DirTablesGet();
							ed.windowManager.open({
								file : t.m_url + '/tablelist.html',
								width : 400,
								height : 500,
								scrollbars : false,
								inline : 1
							}, {
								plugin_url : t.m_url,
								data : data
							});
						} 
					});
					 
					// co => not sel, n => Obj, 
					ed.onNodeChange.add(function(ed, cm, n, co) {
 						cm.setActive('rs_dirtable', n.className == ooki_rs_dirtableObjId);
					});
					
				return c;
			}
		},
		
		_getAttr : function(s, n) {
			var dom = this.editor.dom;
			n = new RegExp(n + '=\"([^\"]+)\"', 'g').exec(s);
			return n ? dom.decode(n[1]) : '';
		},
		
		_parse : function(s) {
			return tinymce.util.JSON.parse('{' + s + '}');
		},
		
		_EditToView : function(c) {
			var t = this;
			var re = new RegExp('<img[^>]* class="'+ooki_rs_dirtableObjId+'"[^>]*>', 'ig');
			
			c = c.replace(re, function(a) {
				var T = t._getAttr(a, "data");
				a = '<script type="text/javascript">ookiWriteRSDirTable({' + T + '});</script>';
				return a;
			});
			return c;
		},
		
		_ViewToEdit : function(ed, o) {
			var t = this, h = o.content;
			var img = tinyMCE.baseURL + '/themes/advanced/img/spacer.gif';
			h = h.replace( /<script[^>]*>ookiWriteRSDirTable\(\{([^\}]*)\}\);\s*<\/script>/ig, function(a, b) {
				aa = t._parse(b);
				a = '<img class="' + ooki_rs_dirtableObjId + '" title="' + aa.title + '" data="' + b + '" src="' + img + '" />';
				return a;
			});
			o.content = h;
		}
	});

	// Register plugin
	tinymce.PluginManager.add('ooki_rs_dirtable', tinymce.plugins.OokiRSDirTable);
})();