var ooki_hidecontentObjId = 'ookiHideContent';

(function() {
	var each = tinymce.each;

	tinymce.create('tinymce.plugins.OokiHidecontent', {
		getInfo : function() {
			return {
				longname : 'HideContent plugin',
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

			
			ed.onSetContent.add(function() {
				t._ViewToEdit(ed.getBody());
			});
			
			ed.onPostProcess.add(function(ed, o) {
				o.content = t._EditToView(o.content);
			});
		},
		
		createControl : function(n, cm) {
			switch (n) {
				case "hidecontent":
					var c, t = this, ed = t.editor;
					c = ed.controlManager.createButton('hidecontent', {
						title : 'ooki_hidecontent.desc',
						image : t.m_url+'/images/hidecontent.gif',
						onclick : function() {
							// Store selection
							ooki_Selection_Store();
								 
							var se = ed.selection;
							var focusElm = se.getNode();
							var selContent = se.getContent();
							var bEdit = t._isObj(focusElm);
							if (bEdit || selContent != "")
							{
								var SOK, SCancel, SInTitle;
								SOK = ed.getLang("ooki_cmn.ok");
								SCancel = ed.getLang("ooki_cmn.cancel");
								SInTitle = ed.getLang("ooki_cmn.t_in_title");
								
								if (bEdit) 
								{
									var C = decodeURIComponent($(focusElm).attr("data"));
									var T = focusElm.innerText.replace("▼", "");
									
									var dlg = 	'<div id="ookiHidecontent" class="ookiPopupIn" style="width:400px; height:300px;">' +
													'<button id="BnAdd">'+SOK+'</button> ' + 
													'<button id="BnCancel">'+SCancel+'</button><br>' +
													SInTitle+'<input id="title" value="' + T + '" size="40">' + 
													'<textarea id="annotationHiddenEditorTM" class="mceEditor" style="width:100%; height:100%;">' + C + '</textarea>' + 
												'</div>';
									
									ooki_Popup_Open( dlg );
									tinyMCE.execCommand("mceAddControl", false, "annotationHiddenEditorTM");
								}
								else
								{
									var dlg = 	'<div id="ookiHidecontent" class="ookiPopupIn" style="width:400px;">' +
													'<button id="BnAdd">'+SOK+'</button> &nbsp;&nbsp;' + 
													'<button id="BnCancel">'+SCancel+'</button><br>' +
													SInTitle+'<input id="title" value="" size="40">' + 
												'</div>';
									
									ooki_Popup_Open( dlg );
									
								}
								$('#ookiHidecontent #BnAdd').click( OnBnAdd );
								$('#ookiHidecontent #BnCancel').click( OnBnCancel );
								
								function OnBnAdd() {
									var C, T;
									T = $('#ookiHidecontent #title').val();
									if (bEdit) 
									{
										C = encodeURIComponent(tinyMCE.get('annotationHiddenEditorTM').getContent());
										OnBnCancel();
										
										$(focusElm).html(T+'▼').attr("data", C);
									}
									else 
									{
										C = encodeURIComponent(selContent);
										OnBnCancel();
										
										var h =	'<div class="' + ooki_hidecontentObjId + '"' + ' data="' + C + '" >' +
													T + '▼' + 
												'</div>';
										ed.execCommand('mceInsertContent', false, h);
									}
								}
		 
								function OnBnCancel() {
									if ( bEdit ) {
										tinyMCE.execCommand('mceRemoveControl', false, "annotationHiddenEditorTM");
									}
									ooki_Popup_Close();
									
									// Restore selection
									ooki_Selection_Restore();
								}
							}										
						} 
					});
					 
					// co => not sel, n => Obj, 
					ed.onNodeChange.add(function(ed, cm, n, co) {
 						cm.setDisabled('hidecontent', co && n.nodeName != 'A');
						cm.setActive('hidecontent', n.nodeName == 'A' && !n.name);
						
						if ( t._isObj(n) ) {
							cm.setActive('hidecontent', true);
							cm.setDisabled('hidecontent', false);
						}
						else {
							cm.setActive('hidecontent', false);
							
							if (co)
								cm.setDisabled('hidecontent', true);
							else
								cm.setDisabled('hidecontent', false);
						}
					});
					
				return c;
			}
		},
		
		_isObj: function(o) {
			return o && o.nodeName == "DIV" && o.className == ooki_hidecontentObjId;
		},
		
		_getAttr : function(s, n) {
			var dom = this.editor.dom;
			n = new RegExp(n + '=\"([^\"]+)\"', 'g').exec(s);
			return n ? dom.decode(n[1]) : '';
		},
		
		_EditToView : function(c) {
			var t = this;
			var Objs = "", ObjId = 0, T, C, oid;
			var re = new RegExp('(<div[^>]* class="'+ooki_hidecontentObjId+'"[^>]*>)(.*?)<\/div>', 'ig');
			
			c = c.replace(re, function(a, b, c) {
				T = c.replace(/▼/g, "");
				C = decodeURIComponent(t._getAttr(b, "data"));
/* 				// 多行
				if ( 0 && C.indexOf("\r\n") > -1 ) {
					oid = ooki_hidecontentObjId + "_" + (++ObjId);
					a = '<span id="' + oid + '" class="' + ooki_hidecontentObjId + '">' + 
							'<span id="title" onclick="ookiHidecontent_Click(this);">' + T + 
								'<span id="sign">▼</span></span>' + 
						'</span>';
					Objs +=	'<div id="' + oid + '" class="' + ooki_hidecontentObjId + '"' + ' style="display:none; ' + ooki_ObjStyle + '" >' + 
								C + 
							'</div>';
				}
				// 單行
				else { */
					a = '<div class="' + ooki_hidecontentObjId + '">' + 
						'<span id="title" onclick="ookiHidecontent_Click(this);" style="white-space:nowrap;">' + T + 
							'<span id="sign">▼</span></span>' + 
						'<div id="content" style="display:none; ' + ooki_ObjStyle + '">' + C + '</div>' + 
					'</div>';
				//}
				return a;
			});
			return Objs + c;
		},
		
		_ViewToEdit : function(p) {
			var t = this;
			
			p = $(p);
			p.find('div.'+ooki_hidecontentObjId).each(function() {
				var oid, T, C, n = $(this), o;
				n.find('#sign').remove();
				T = n.find('#title').html();
				if ( T ) {
	/* 				oid = n.attr("id");
					if ( oid ) {
						o = p.find('div#'+oid);
						C = encodeURIComponent(o.html());
						o.remove();
					}
					else { */
						C = encodeURIComponent(n.find('#content').html());
					//}
					n.html(T+'▼').attr("data", C);
				}
			});
			
			// 刪除舊格式移漏的物件
			//p.find('div.'+ooki_hidecontentObjId).remove();
		}
	});

	// Register plugin
	tinymce.PluginManager.add('ooki_hidecontent', tinymce.plugins.OokiHidecontent);
})();