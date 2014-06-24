var ooki_imgresizeObjId = 'ooki_imgresizeTM';

(function() {
	tinymce.create('tinymce.plugins.OokiImgResize', {
		getInfo : function() {
			return {
				longname : 'Image Resize plugin',
				author : 'Monkia',
				authorurl : 'http://ookon.nuweb.cc',
				infourl : 'http://ookon.nuweb.cc',
				version : '2.0'
			};
		},
	
		init : function(ed, url) {
			var t = this;
			t.editor = ed;
			
		},
		
		createControl : function(n, cf) {
			switch (n) {
				case "imgresize":
					var btn, t = this, ed = t.editor;
					btn = ed.controlManager.createListBox('imgresize', {
						title : 'ooki_imgresize.desc', 
						onselect : function(v) {
							var n = ed.selection.getNode(), $n=$(n), w, h;
							if ( n.nodeName == 'IMG' ) {
								w = parseInt(v);
								if ( isNaN(w) ) return;
								if( n.className.indexOf("mceItem") > -1) {
									n.width = w;
									n.height = w/4*3;
									n._mce_style = "";
								}
								else {
									if (w > 0) {
										n.style.width = w;
										n.style.height = "auto";
									}
									else {
										n.style.width = "auto";
										n.style.height = "auto";
									}
									h = $n.height();
									if (h == 0) h = (w/4)*3;
									n.style.height = h;
									n._mce_style = "";
									
									n.width = w;
									n.height = h;
									
									// IE 
									if (navigator.userAgent.match(/(msie) ([\w.]+)/i)) {
										var Shell = new ActiveXObject("WScript.Shell");
										var sPath = Shell.RegWrite("HKCU\\Software\\Monkia\\NUWeb\\NUBraim\\AE_img_width", w, "REG_DWORD");
									}
								}
							}
						} 
					});
					
 					btn.add('ooki_imgresize.primitive_size', -1); 
					for (x=40; x<=800; x+=40) {
						btn.add(x, x); 
					}
					
					// co => not sel, n => Obj, 
					ed.onNodeChange.add(function(ed, cm, n, co) {
						if ( n.nodeName == 'IMG' ) {
							cm.setDisabled('imgresize', false);
							btn.select($(n).width());
						}
						else
							cm.setDisabled('imgresize', true);
					});
					
				return btn;
			}
		}
	});

	
	// Register plugin
	tinymce.PluginManager.add('ooki_imgresize', tinymce.plugins.OokiImgResize);
})();