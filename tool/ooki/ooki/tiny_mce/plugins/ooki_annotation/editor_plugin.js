var ooki_annotationObjId = 'ooki_annotationTM';
var ooki_annotationObjStyle = 'border-bottom: red 1px solid;';

(function() {
	tinymce.create('tinymce.plugins.OokiAnnotation', {
		getInfo : function() {
			return {
				longname : 'Annotation plugin',
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
		
		createControl : function(n, cf) {
			switch (n) {
				case "annotation":
					var c, t = this, ed = t.editor;
					c = ed.controlManager.createButton('annotation', {
						title : 'ooki_annotation.desc',
						image : t.m_url+'/images/annotation.gif',
						onclick : function() {
							// Store selection
							ooki_Selection_Store();
							
							var se = ed.selection;
							var focusElm = se.getNode();
							var selTitle = se.getContent({no_events: true});
							var selContent = "";
							var bAHE = ( ed.dom.getParent(focusElm, 'SPAN') && ed.dom.getAttrib(focusElm, 'class') == ooki_annotationObjId );
							if (selTitle != "" || bAHE)
							{
								if (bAHE) {
									selContent = decodeURIComponent($(focusElm).attr("annotationdata"));
								}
								
								var dlg =	'<div id="ookiAnnotation" class="ookiPopupIn" style="width:800px; height:400px; border:solid 1px #999;">' +
												'<textarea id="annotationHiddenEditorTM" class="mceEditor" style="width:500px; height:350px;">'+selContent+'</textarea>' + 
												'<div style="text-align:right; padding:5px;">' +
													'<button id="BnAdd">'+ed.getLang('ooki_cmn.ok')+'</button> &nbsp; ' + 
													'<button id="BnCancel">'+ed.getLang('cancel')+'</button><br>' +
												'</div>' + 
											'</div>';
								ooki_Popup_Open( dlg );
								tinyMCE.execCommand("mceAddControl", false, "annotationHiddenEditorTM");
								
								$('#ookiAnnotation #BnAdd').click( function(ev) {
									var C = encodeURIComponent($.trim(tinyMCE.get('annotationHiddenEditorTM').getContent()));
									tinyMCE.execCommand('mceRemoveControl', false, "annotationHiddenEditorTM");
									ooki_Popup_Close();
									// Restore selection
									ooki_Selection_Restore();
									
									if (C == "")
									{
										if (bAHE) {
											selTitle = $(focusElm).text();
											ed.execCommand("mceBeginUndoLevel");
											$(focusElm).before(selTitle).remove();
											ed.execCommand("mceEndUndoLevel");
										}
									}
									else
									{
										if (bAHE) {
											ed.execCommand("mceBeginUndoLevel");
											$(focusElm).attr("annotationdata", C);
											ed.execCommand("mceEndUndoLevel");
										}
										else {
											var h = '<span class="' + ooki_annotationObjId + '"' +
													' style="' + ooki_annotationObjStyle + '"' + 
													' annotationdata="' + C + '"' +
													' >' + selTitle + '</span>';
											ed.execCommand('mceInsertContent', false, h);
											// 有包到兩層, 清潔內層.
											$(focusElm).find("."+ooki_annotationObjId).each(function(){
												$(this).find("."+ooki_annotationObjId).each(function(){
													var $t=$(this);
													$t.before($t.text()).remove();
												});
											});
										}
									}
								});
								
								$('#ookiAnnotation #BnCancel').click( function(ev) {
									tinyMCE.execCommand('mceRemoveControl', false, "annotationHiddenEditorTM");
									ooki_Popup_Close();
									// Restore selection
									ooki_Selection_Restore();
								});
							}											
							
						} 
					});
					 
					// co => not sel, n => Obj, 
					ed.onNodeChange.add(function(ed, cm, n, co) {
						if (!co || t._isObj(n))
							cm.setDisabled('annotation', false);
						else
							cm.setDisabled('annotation', true);
					});
					
				return c;
			}
		},
		
		_isObj: function(o) {
			return o && o.nodeName == "SPAN" && o.className == ooki_annotationObjId;
		},
		
		_getAttr : function(s, n) {
			var dom = this.editor.dom;
			n = new RegExp(n + '=\"([^\"]+)\"', 'g').exec(s);
			return n ? dom.decode(n[1]) : '';
		},
		
		_EditToView : function(c) {
			var t = this, dom = t.editor.dom;
			var Objs = "", ObjId = 0;
			var re = new RegExp('(<span[^>]* class="'+ooki_annotationObjId+'"[^>]*>)(.*?)<\/span>', 'ig');
			//c = ooki_content_aftDelSpace(c);
			
			c = c.replace(re, function(a, b, T) {
				var C = decodeURIComponent(t._getAttr(b, "annotationdata"));
				var style = $.trim(t._getAttr(b, "style"));
				style = style.replace(ooki_annotationObjStyle, "").replace(ooki_annotationObjStyle, "");
				var oid = ooki_annotationObjId + "_" + (++ObjId);
				a = '<span id="' + oid + '" class="' + ooki_annotationObjId + '"' + 
						' onmouseover="ookiAnnotated_MouseOver(this);" onmouseout="ookiAnnotated_MouseOut(this);"' + 
						' style="' + ooki_annotationObjStyle + style + '">' + 
						T + 
					'</span>';
				Objs += '<div id="' + oid + '" class="' + ooki_annotationObjId + '" style="display:none; border:1px dotted #cc0000; background-color:#ffffcc;">' +
						C + 
						'</div>';
				return a;
			});
			return Objs + c;
		},
		
		_ViewToEdit : function(p) {
			var t = this, dom = t.editor.dom;
			var $p = $(p);
			
			$p.find('span.'+ooki_annotationObjId).each(function() {
				var $t=$(this), oid, C, $o;
				this.onmouseover = "";
				this.onmouseout = "";
				oid = $t.attr("id");
				if (!oid) return;
				$o = $p.find('div#'+oid);
				C = encodeURIComponent($o.html());
				$o.remove();
				$t.attr("annotationdata", C);
			});
			
			// 刪除舊格式移漏的物件
			$p.find('div.'+ooki_annotationObjId).remove();

		}
	});

	// Register plugin
	tinymce.PluginManager.add('ooki_annotation', tinymce.plugins.OokiAnnotation);
})();