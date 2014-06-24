var ooki_bgsoundObjId = 'ookiBGSoundTM';

(function() {
	var each = tinymce.each;

	tinymce.create('tinymce.plugins.OokiBGSound', {
		getInfo : function() {
			return {
				longname : 'Background Sound plugin',
				author : 'Monkia',
				authorurl : 'http://ookon.nuweb.cc',
				infourl : 'http://ookon.nuweb.cc',
				version : '2.0'
			};
		},
	
		init : function(ed, url) {
			var t = this, DOM = tinymce.DOM;
			
			t.editor = ed;
			t.m_bgsound_ext = ',wav,mp3,';
			t.m_url = url;


			// Register buttons
			ed.addButton('bgsound', {
				title : 'ooki_bgsound.desc',
				image : url+'/images/bgsound.gif',
				onclick : function() 
				{
					var n = ed.selection.getNode();
					if ( t._isObj(n) )
					{
						var h, u, fn, T;
						u = n.href;
						fn = decodeURIComponent( B_URL_MakeFileName(u) );
						T = u + "," + fn;
						h = '<img class="' + ooki_bgsoundObjId + 
								'" title="'+ T + 
								'" src="' + url +'/images/bgsound_b.gif">';
						ed.execCommand('mceSelectNode', false, n);
						ed.execCommand('mceInsertContent', false, "");
						
						$(ed.getDoc()).find('body').prepend(h);
					}
				}
			});

			//ed.addShortcut('ctrl+k', 'advlink.advlink_desc', 'mceAdvLink');

			// co => not sel, n => Obj, 
			ed.onNodeChange.add(function(ed, cm, n, co) {
				if ( !t._isSet() && t._isObj(n) )
					cm.setDisabled('bgsound', false);
				else
					cm.setDisabled('bgsound', true);
			});
			
			ed.onSetContent.add(function() {
				t._ViewToEdit(ed.getBody());
			});
			// 貼上資料之前
			ed.onPreProcess.add(function(ed, o) {
				//alert("onPreProcess");
			});
			ed.onBeforeSetContent.add(function(ed, o) {
				//alert("onBeforeSetContent : \n" + ed.getBody().innerHTML);
			});
			
			ed.onPostProcess.add(function(ed, o) {
				o.content = t._EditToView(o.content);
			});
			
		},
		
		_isObj : function(n) {
			var t = this, ext;
			
			if ( n.nodeName != "A" ) 
				return null;
			ext = B_URL_MakeExtension(n.href).toLowerCase();
			if ( ext == "" ) return null;
			return t.m_bgsound_ext.indexOf( ','+ext+',' ) > -1;
		},
		
		_isSet : function() {
			var ed = this.editor;
			return $(ed.getBody()).find('.'+ooki_bgsoundObjId).length > 0;
		},
		
		_getAttr : function(s, n) {
			var dom = this.editor.dom;
			n = new RegExp(n + '="([^"]*)"', 'i').exec(s);
			return n ? dom.decode(n[1]) : '';
		},
		
		_EditToView : function(c) {
			var t = this, ed = t.editor;
			
 			c = c.replace(/<img [^>]+>/ig, function(a) {
				if (t._getAttr(a, 'class') == ooki_bgsoundObjId) {
					var T;
					T = t._getAttr(a, 'title');
					ra = T.split(',', 2);
					
					a = '<span class="' + ooki_bgsoundObjId + 
								'" style="' + ooki_ObjStyle + '">' + 
								'<a href="' + ra[0] + '" title="' + ed.getLang('ooki_bgsound.title') + '">' + B_con_String2Html(ra[1]) + '</a>' + 
							'</span>';
				}
				return a;
			});
			return c;
		},
		
		_ViewToEdit : function(p) {
			var t = this;
			
 			p = $(p);
			p.find('span.'+ooki_bgsoundObjId).each(function() {
				var n = $(this), oA, u, fn, T, h;
				
				oA = n.find('a');
				fn = oA.html();
				u = oA.attr("href");
				
				T = u + "," + fn;
				h = '<img class="' + ooki_bgsoundObjId + 
						'" title="'+ T + 
						'" src="' + t.m_url +'/images/bgsound_b.gif">';
				
				n.before(h).remove();
			});
		}
	});

	// Register plugin
	tinymce.PluginManager.add('ooki_bgsound', tinymce.plugins.OokiBGSound);
})();