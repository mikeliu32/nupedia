var ooki_imageleftObjId = 'ooki_imageleftTM';

(function() {
	var each = tinymce.each;

	tinymce.create('tinymce.plugins.OokiImageleft', {
		getInfo : function() {
			return {
				longname : 'ImageLeft plugin',
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

		},
		
		createControl : function(n, cf) {
			switch (n) {
				case "imageleft":
					var c, t = this, ed = t.editor;
					c = ed.controlManager.createButton('imageleft', {
						title : 'ooki_imageleft.desc',
						image : t.m_url + '/images/imageleft.gif',
						onclick : function() {
							var se = ed.selection;
							var focusElm = se.getNode();
							var selHtml = se.getContent();
							if (selHtml != "")
							{
								var h, oid, o, doc, trng;
								oid = "ooki_imageleft_" + (new Date()).getTime();
								h = 	'<div id="' + oid + '" class="' + ooki_imageleftObjId + '" style="border:solid 1px black;float:left">' + 
											'<div style="padding:5px;margin:5px;float:left;">' + selHtml + '</div>' + 
											'<div>text</div>' + 
										'</div>';
								ed.execCommand('mceInsertContent', false, h);
								
								// 標示選取
								doc = ed.getBody();
								o = $(doc).find('#'+oid).get(0);
								trng = doc.createTextRange();
								trng.moveToElementText(o);
								trng.select();
								
							}				
						}
					});
					 
					// co => not sel, n => Obj, 
					ed.onNodeChange.add(function(ed, cm, n, co) {
						if (co)
							cm.setDisabled('imageleft', true);
						else
							cm.setDisabled('imageleft', false);
					});
					
				return c;
			}
		}
		
	});

	// Register plugin
	tinymce.PluginManager.add('ooki_imageleft', tinymce.plugins.OokiImageleft);
})();