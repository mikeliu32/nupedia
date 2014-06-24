(function() {
	tinymce.create('tinymce.plugins.OokiMeta', {
		getInfo : function() {
			return {
				longname : 'Meta plugin',
				author : 'Monkia',
				authorurl : 'http://ookon.nuweb.cc',
				infourl : 'http://ookon.nuweb.cc',
				version : '2.0'
			};
		},
	
		init : function(ed, url) {
			var t = this;
			t.editor = ed;
			
			if ( !tinymce.ooki ) tinymce.ooki = {};
			if ( !tinymce.ooki.meta ) tinymce.ooki.meta = {};

			// Register buttons
			ed.addButton('meta', {
				title : 'ooki_meta.desc',
				image : url+'/images/meta.gif',
				onclick : function() 
				{
					ed.windowManager.open({
						file : url + '/meta.html',
						width : 300,
						height : 370,
						scrollbars : false,
						inline : 1
					}, {
						plugin_url : url
					});
				}
			});

			ed.onSetContent.add(function() {
				t._ViewToEdit(ed.getBody());
			});
			
			// ed.onPostProcess.add(function(ed, o) {
				// o.content = t._EditToView(o.content);
			// });

			// co => not sel, n => Obj, 
			ed.onNodeChange.add(function(ed, cm, n, co, o) {
				//cm.setDisabled('meta', false);
			});
		},
		
		_EditToView : function(c) {
		/*
			var t = this, h="", hT="";
			
			for (n in tinymce.ooki.meta) {
				if ( tinymce.ooki.meta[n].length ) {
					h += '<meta name="' + n + '" content="' + tinymce.ooki.meta[n] + '">';
					if ( n == "title" ) 
						hT = '<title> ' + tinymce.ooki.meta[n] + ' </title>';
				}
			}
			return hT + h + c;
		*/
		},
		
		_ViewToEdit : function(p) {
			var t = this, p = $(p);
			
			p.find("meta").each( function() {
				var o = $(this), n, v;
				n = o.attr("name");
				v = o.attr("content");
				//alert( n + " = " + v );
				if ( n.length ) 
					tinymce.ooki.meta[n] = v;
				o.remove();
			});
		}		
	});

	// Register plugin
	tinymce.PluginManager.add('ooki_meta', tinymce.plugins.OokiMeta);
})();