tinyMCE.init({
	mode:"textareas",
	theme : "advanced",
	editor_selector:"mceEditor",
	language:"tw",
	plugins:"createpage, internallink, totable, showhide, sandbox, physicallink, pediasearchlink, othersearchlink, newssearchlink, imgbox, imagesearchlink, imageleft, glossary, externalsearchlink, deleteline, clearall, annotation, table, searchreplace, media, webcam",
	content_css:"/ooki/tiny_mce/plugins/plugin_editor.css",
	width:"100%",
	height:"300",
	media_use_script:true,
	theme_advanced_fonts:"\u65b0\u7d30\u660e\u9ad4=\u65b0\u7d30\u660e\u9ad4,\u7d30\u660e\u9ad4;\u6a19\u6977\u9ad4=\u6a19\u6977\u9ad4,Andale Mono=andale mono,times;Arial=arial,helvetica,sans-serif;Arial Black=arial black,avant garde;Book Antiqua=book antiqua,palatino;Comic Sans MS=comic sans ms,sand;Courier New=courier new,courier;Georgia=georgia,palatino;Helvetica=helvetica;Impact=impact,chicago;Symbol=symbol;Tahoma=tahoma,arial,helvetica,sans-serif;Terminal=terminal,monaco;Times New Roman=times new roman,times;Trebuchet MS=trebuchet ms,geneva;Verdana=verdana,geneva;Webdings=webdings;Wingdings=wingdings,zapf dingbats",
	theme_advanced_toolbar_location:"top",
	theme_advanced_toolbar_align:"left",
	theme_advanced_buttons1:"bold, italic, underline, strikethrough, separator, forecolor, backcolor, separator, image, link, unlink, flash, separator, cut, copy, paste, separator, undo, redo, separator, fontselect, fontsizeselect, separator, media, separator, code, audio, video",
	theme_advanced_buttons2:"justifyleft, justifycenter, justifyright, justifyfull, separator, outdent, indent, separator, bullist, numlist, separator, sub, sup, separator, hr, charmap, separator, search, replace, separator, visualaid, separator, tablecontrols, separator, clearall, deleteline",
	theme_advanced_buttons3:"annotation, createpage, imageleft, totable, imgbox, glossary, internallink, physicallink, showhide, externalsearchlink, imagesearchlink, newssearchlink, pediasearchlink, othersearchlink, sandbox",
	theme_advanced_statusbar_location:"bottom",
	theme_advanced_resizing:true,
	theme_advanced_resize_horizontal:false,
	theme_advanced_resizing_use_cookie:false,
	extended_valid_elements:"script[src|language],iframe[src|frameborder|width|height]," +
							"+a[id|style|rel|rev|charset|hreflang|dir|lang|tabindex|accesskey|type|name|" +
							"href|target|title|class|onfocus|onblur|onclick|annotationdata|" +
							"ondblclick|onmousedown|onmouseup|onmouseover|onmousemove|onmouseout|onkeypress|onkeydown|onkeyup]," + 
							"div[id|dir|class|align|style|title|onclick]," +
							"span[style|class|align|id|title|onclick|annotationdata|onmouseover|onmouseout|hidecontentData]," +
							"embed[src|width|height|type|wmode]," +
							"img[style|id|class|align|title|src|arg|onload|border|width|height]" + 
							"height|align|summary|bgcolor|background|bordercolor|class]," +
							"input[type|size|title|value|checked|disabled]," +
							"select[size],option[value]" +
							"center",
	force_br_newlines : true, 
	force_p_newlines : false,
	convert_urls : false,
	convert_fonts_to_spans:true});
