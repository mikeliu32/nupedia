var ooki_imgboxObjId = 'ooki_imgboxTM';

(function() {
	var each = tinymce.each;

	tinymce.create('tinymce.plugins.OokiImgBox', {
		getInfo : function() {
			return {
				longname : 'ImgBox plugin',
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
				case "imgbox":
					var c, t = this, ed = t.editor;
					c = ed.controlManager.createButton('imgbox', {
						title : 'ooki_imgbox.desc',
						image : t.m_url + '/images/imgbox.gif',
						onclick : function() 
						{
							// Store selection
							ooki_Selection_Store();
								 
							var se = ed.selection;
							var n = se.getNode();
							var selHtml = se.getContent();
							var o = t._isObj(n);
							if (o || n.nodeName == "IMG")
							{
								var dlg;
								var SOK, SCancel, SInTitle;
								SOK = ed.getLang("ooki_cmn.ok");
								SCancel = ed.getLang("ooki_cmn.cancel");
								SInTitle = ed.getLang("ooki_cmn.t_in_title");
								
								if (o) 
								{
									dlg = 	'<div id="ookiImgBox" class="ookiPopupIn" style="position:absolute;padding:10px;">' +
												'<button id="BnCancel">'+SCancel+'</button><br>' +
												'<a class="BnAlign" rel="left" href=# >靠左對齊</a><br>' +
												'<a class="BnAlign" rel="right" href=# >靠右對齊</a><br>' + 
												'<a class="BnAlign" rel="del" href=# >刪除方塊</a><br>' + 
											'</div>';
								}
								else
								{
									dlg = 	'<div id="ookiImgBox" class="ookiPopupIn" style="position:absolute;padding:10px;">' +
												'<button id="BnCancel">'+SCancel+'</button><br>' +
												'<a class="BnAlign" rel="left" href=# >靠左對齊</a><br>' +
												'<a class="BnAlign" rel="right" href=# >靠右對齊</a><br>' + 
											'</div>';
									
								}
								ooki_Popup_Open( dlg );
								$('#ookiImgBox #BnCancel').click( OnBnCancel );
								$('#ookiImgBox .BnAlign').click( OnBnAlign );
								
								function OnBnAlign() {
									var rel, h, oid, doc, trng;
									rel = $(this).attr("rel");
									OnBnCancel();
									
									if ( o )
									{
										if ( rel == "del" ) {
											var C = $(o).html();
											$(o).before(C).remove();
										}
										else {
											$(o).css("float", rel);
										}
									}
									else
									{
										oid = 	"ooki_imgbox_"+(new Date()).getTime();
										h = 	'<div id="' + oid + '" class="' + ooki_imgboxObjId + '" style="float:' + rel + '; border:1px solid black; padding:3px; margin:10px; text-align:center;" >' + 
													selHtml + 
//													'<div>Content</div>' +
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
								
								function OnBnCancel() {
									ooki_Popup_Close();
									
									// Restore selection
									ooki_Selection_Restore();
								}
							}								
						}
					});
					 
					// co => not sel, n => Obj, 
					ed.onNodeChange.add(function(ed, cm, n, co) {
						if ( t._isObj(n) ) {
							cm.setActive('imgbox', true);
							cm.setDisabled('imgbox', false);
						}
						else {
							cm.setActive('imgbox', false);
							
							if ( n.nodeName == "IMG" )
								cm.setDisabled('imgbox', false);
							else
								cm.setDisabled('imgbox', true);
						}
					});
			
				return c;
			}
		},
		
		_isObj: function(o) {
			while ( o && (o.nodeName != "DIV" || o.className != ooki_imgboxObjId) )
				o = o.parentNode;
			return o;
		}
		
	});

	// Register plugin
	tinymce.PluginManager.add('ooki_imgbox', tinymce.plugins.OokiImgBox);
})();