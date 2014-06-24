
(function() {

	// 只能在 NUBraim 上面使用
	if ( !ooki_bCXHtmlView ) 
		return;
				

	tinymce.create('tinymce.plugins.OokiCreateArticle', {
		getInfo : function() {
			return {
				longname : 'Create Article plugin',
				author : 'Monkia',
				authorurl : 'http://ookon.nuweb.cc',
				infourl : 'http://ookon.nuweb.cc',
				version : '2.0'
			};
		},
	
		init : function(ed, url) {
			try {external.B_GetVersion();} 
			catch(e){return true;}

			var t = this;
			t.editor = ed;

			// Register commands
			//ed.addCommand('mceAudio', function() {
			//});

			// Register buttons
			// createdir
			ed.addButton('createdir', {
				title : 'ooki_create_article.createdir_desc',
				image : url+'/images/createdir.gif',
				onclick : function() 
				{
					var dom = ed.dom;
					var focusElm = ed.selection.getNode();
					var selectedText = ed.selection.getContent({format:'text'});
					switch (focusElm.nodeName) 
					{
					case "A":
						selectedText = focusElm.innerText;
						if ( selectedText != "" ) {
							var U = external.AE_B_CreateDir(selectedText, "");
							if ( U != "" ) {
								ed.execCommand("mceBeginUndoLevel");
								dom.setAttrib(focusElm, 'href', U);
								ed.execCommand("mceEndUndoLevel");
							}
						}
						break;
						
					case "TR":
						var ns = "";
						var cols = focusElm.cells;
						var l = cols.length;
						for (x=0; x<l; x++) {
							if (ns.length) ns += "<br>";
							ns += cols(x).innerText;
						}
						
						var Us = external.AE_B_CreateDir(ns, "").split("<br>");
						if (Us.length == l) {
							ed.execCommand("mceBeginUndoLevel");
							for (x=0; x<l; x++) {
								if (Us[x].length) 
									dom.setAttrib(cols(x), 'innerHTML', "<a href=\"" + Us[x] + "\">" + cols(x).innerText + "</a>" );
							}
							ed.execCommand("mceEndUndoLevel");
						}
						else {
							alert("error: create.");
						}
						break;
						
					case "TBODY":
						if (focusElm.parentNode.nodeName == "TABLE")
						{
							var ns = "";
							var l = 0;
							var rows = focusElm.parentNode.rows;
							var lr = rows.length;
							for (xr=0; xr<lr; xr++) {
								var cols = rows(xr).cells;
								var lc = cols.length;
								for (xc=0; xc<lc; xc++) {
									if (ns.length) ns += "<br>";
									ns += cols(xc).innerText;
									l++;
								}
							}
							
							var Us = external.AE_B_CreateDir(ns, "").split("<br>");
							if (Us.length == l) {
								ed.execCommand("mceBeginUndoLevel");
								l = 0;
								for (xr=0; xr<lr; xr++) {
									var cols = rows(xr).cells;
									var lc = cols.length;
									for (xc=0; xc<lc; xc++) {
										if (Us[l].length) 
											dom.setAttrib(cols(xc), 'innerHTML', "<a href=\"" + Us[l] + "\">" + cols(xc).innerText + "</a>" );
										l++;
									}
								}
								ed.execCommand("mceEndUndoLevel");
							}
							else {
								alert("error: create.");
							}
						}
						break;
						
					case "TD":
					default:
						if (selectedText.length) {
							// Store selection
							ooki_Selection_Store();

							var func = function(mode) {
								// Restore selection
								ooki_Selection_Restore();
								
								var U = external.AE_B_CreateDir(selectedText, mode);
								if (U.length) {
									var h = "<a href=\"" + U + "\">" + selectedText + "</a>";
									ed.execCommand('mceInsertContent', false, h);
								}
							}
							
							// if ( typeof(ooki_CreateArticle_getDirMode) == "function" )
								// ooki_CreateArticle_getDirMode(func);
							// else
								func("");
							return;
						}
						break;
					}
				}
			});
			
			// Createpage
			ed.addButton('createpage', {
				title : 'ooki_create_article.createpage_desc',
				image : url+'/images/createpage.gif',
				onclick : function() 
				{
					var dom = ed.dom;
					var focusElm = ed.selection.getNode();
					var selectedText = ed.selection.getContent({format:'text'});
					if (focusElm && focusElm.nodeName == "A") 
					{
						selectedText = focusElm.innerText;
						if ( selectedText != "" ) {
							var sFile = external.AE_B_CreatePage(selectedText);
							if (sFile.length) {
								ed.execCommand("mceBeginUndoLevel");
								dom.setAttrib(focusElm, 'href', sFile);
								ed.execCommand("mceEndUndoLevel");
							}
						}
					} 
					else if (selectedText != "")
					{
						var sFile = external.AE_B_CreatePage(selectedText);
						if (sFile.length) {
							var h = "<a href=\"" + sFile + "\">" + selectedText + "</a>";
							ed.execCommand('mceInsertContent', false, h);
						}
					}

				}
			});
	
			// SelectToPage
			ed.addButton('selecttopage', {
				title : 'ooki_create_article.STP_desc',
				image : url+'/images/selecttopage.gif',
				onclick : function() 
				{
					var selHtml = ed.selection.getContent();
					if ( selHtml != "" ) {
						var sInTitle = ed.getLang("ooki_create_article.STP_input_title");
						var T = prompt(sInTitle, ""); //"請輸入文章標題："
						if (T && T != "")
						{
							var sFile = external.AE_B_SelectToPage(T, selHtml);
							if ( sFile != "" ) {
								var h = "<a href=\"" + sFile + "\">" + T + "</a>";
								ed.execCommand("mceInsertContent", false, h);
							}
						}
					}
				}
			});
	
			//ed.addShortcut('ctrl+k', 'advlink.advlink_desc', 'mceAdvLink');

			// co => not sel, n => Obj, 
			ed.onNodeChange.add(function(ed, cm, n, co) {
				cm.setDisabled('createdir', !(n.nodeName == "A" || !co));
				cm.setDisabled('createpage', !(n.nodeName == "A" || !co));
				cm.setDisabled('selecttopage', co);
			});
			
		}
		
	});

	// Register plugin
	tinymce.PluginManager.add('ooki_create_article', tinymce.plugins.OokiCreateArticle);
})();