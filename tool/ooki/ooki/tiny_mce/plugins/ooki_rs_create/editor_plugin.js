
(function() {

	tinymce.create('tinymce.plugins.OokiRSCreate', {
		getInfo : function() {
			return {
				longname : 'RS Create plugin',
				author : 'Monkia',
				authorurl : 'http://ookon.nuweb.cc',
				infourl : 'http://ookon.nuweb.cc',
				version : '2.0'
			};
		},
	
		init : function(ed, url) {

			var t = this;
			t.editor = ed;
			
			// Register buttons
			// createdir
			ed.addButton('rs_createdir', {
				title : 'ooki_rs_create.createdir_desc',
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
							var h, u_fp, fp = file_api_con_fn2fp(selectedText);
							if (fp != "") {
								u_fp = rs_con_FilePath2UrlPath("Site", fp);
								h = "<a href=\"" + u_fp + "\">" + selectedText + "</a>";
								ed.execCommand('mceInsertContent', false, h);
								return;
							}
							
							file_api_create_dir("", selectedText, "",
								function(data){
									var u_fp = data;
									if (u_fp.length) {
										ed.execCommand("mceBeginUndoLevel");
										dom.setAttrib(focusElm, 'href', u_fp);
										ed.execCommand("mceEndUndoLevel");
									
										fp = rs_con_UrlPath2FilePath(u_fp);
										file_filelist_add(fp, selectedText);
									}
								},
								function(data){
									alert(data);
								}
							);
						}
						break;
						
/*					case "TR":
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
*/						
					case "TD":
					default:
						if (selectedText.length) {
							var h, u_fp, fp = file_api_con_fn2fp(selectedText);
							if (fp != "") {
								u_fp = rs_con_FilePath2UrlPath("Site", fp);
								h = "<a href=\"" + u_fp + "\">" + selectedText + "</a>";
								ed.execCommand('mceInsertContent', false, h);
								return;
							}
						
							file_api_create_dir("", selectedText, "",
								function(data){
									u_fp = data;
									if (u_fp.length) {
										h = "<a href=\"" + u_fp + "\">" + selectedText + "</a>";
										ed.execCommand('mceInsertContent', false, h);
										
										fp = rs_con_UrlPath2FilePath(u_fp);
										file_filelist_add(fp, selectedText);
									}
								},
								function(data){
									alert(data);
								}
							);
						}
						break;
					}
				}
			});
			
			// Create page
			ed.addButton('rs_createpage', {
				title : 'ooki_rs_create.createpage_desc',
				image : url+'/images/createpage.gif',
				onclick : function() 
				{
					var dom = ed.dom;
					var focusElm = ed.selection.getNode();
					var selectedText = ed.selection.getContent({format:'text'});
					if (focusElm && focusElm.nodeName == "A") 
					{
						// selectedText = focusElm.innerText;
						// if ( selectedText != "" ) {
							// var sFile = external.AE_B_CreatePage(selectedText);
							// if (sFile.length) {
								// ed.execCommand("mceBeginUndoLevel");
								// dom.setAttrib(focusElm, 'href', sFile);
								// ed.execCommand("mceEndUndoLevel");
							// }
						// }
					} 
					else if (selectedText != "")
					{
						var h, u_fp, fp, path, title, content;
						title = selectedText;
						name = B_FileName_Filter(title);
						if (name.length > 64) name = name.substr(0, 64);
						//name = file_filelist_getOnlyFileName(g_upload_file_arg.file_path, name, 'html');
						name += ".html";
						
						// 已經存在了
						fp = file_api_con_fn2fp(name);
						if (fp != "") {
							u_fp = rs_con_FilePath2UrlPath("Site", fp);
							h = "<a href=\"" + u_fp + "\">" + title + "</a>";
							ed.execCommand('mceInsertContent', false, h);
							
							path = B_URL_MakePath(fp, false, false);
							fn = B_URL_MakeFileName(fp, true);
							file_api_open_editer(path, fn);
							return;
						}
						// 新增文章
						content = '<title>'+title+'</title>\r\n';
						file_api_create_article("", name, content,
							function(data){
								var u_fp = data;
								if (u_fp.length) {
									h = "<a href=\"" + u_fp + "\">" + title + "</a>";
									ed.execCommand('mceInsertContent', false, h);
									
									fp = rs_con_UrlPath2FilePath(u_fp);
									file_filelist_add(fp, name);
									
									path = B_URL_MakePath(fp, false, false);
									fn = B_URL_MakeFileName(fp, true);
									file_api_open_editer(path, fn);
								}
							},
							function(data){
								alert(data);
							}
						);
					}

				}
			});
	
			// SelectToPage
			ed.addButton('rs_selecttopage', {
				title : 'ooki_rs_create.STP_desc',
				image : url+'/images/selecttopage.gif',
				onclick : function() 
				{
					var selHtml = ed.selection.getContent();
					if ( selHtml != "" ) {
						var sInTitle = ed.getLang("ooki_rs_create.STP_input_title");
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
				cm.setDisabled('rs_createdir', !(n.nodeName == "A" || !co));
				cm.setDisabled('rs_createpage', !(n.nodeName == "A" || !co));
				cm.setDisabled('rs_selecttopage', co);
			});
			
		}
	});

	// Register plugin
	tinymce.PluginManager.add('ooki_rs_create', tinymce.plugins.OokiRSCreate);
})();