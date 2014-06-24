var InternalSearchDialog = {
	preInit : function() {
		var url;

		tinyMCEPopup.requireLangPack();

		if (url = tinyMCEPopup.getParam("external_image_list_url"))
			document.write('<script language="javascript" type="text/javascript" src="' + tinyMCEPopup.editor.documentBaseURI.toAbsolute(url) + '"></script>');
	},

	init : function(ed) {
		var t = this, ed = tinyMCEPopup.editor, n = ed.selection.getNode(), dom = ed.dom;
		var f = document.forms[0], nl = f.elements;
		
		t.plugin_url = tinyMCEPopup.getWindowArg("plugin_url");
		t.m_bSelObj = false;
		t.m_selText = ed.selection.getContent({format:'text'}).replace(/^\s+|\s+$/g,"");
		t.target_mode = tinyMCEPopup.getWindowArg("target_mode");
		t.target_arg = tinyMCEPopup.getWindowArg("target_arg");
		t.search_p = 1;
		t.search_ps = 10;
		
		this.g_tabSel = "general_tab";
		
		tinyMCEPopup.resizeToInnerSize();
		
		$( 'body' ).css('overflow', 'hidden' ).height( '100%' );
		$( 'body' ).attr('scroll', 'no');
		
		if (n && n.nodeName == "A") {
			t.m_selText = n.title;
			if (!t.m_selText.length)
				t.m_selText = n.innerText;
		}
		
		f.IS_Key.value = t.m_selText;
		f.q.value = t.m_selText;
		
	},

	insert : function() {
		var t = this, ed = tinyMCEPopup.editor, dom = ed.dom, f = document.forms[0];
		var u = "", q = "";
		var focusElm = ed.selection.getNode();
		var selText = ed.selection.getContent({format:"text"});
		var arg = t.target_arg;

		switch (t.g_tabSel) {
			case "general_tab":
				q = $("#general_panel #IS_Key").val();
				if (!q.length) {
					alert(tinyMCEPopup.getLang('ooki_internalsearch_dlg.InputKey')); //請輸入所要查詢的字詞
					f.q.focus();
					return;
				}
				if (t.target_mode == "rs") {
					u = "http://" + arg.host + "/tools/pv/search.php" + 
							"?site=" + arg.site + 
							"&file_path=" + encodeURIComponent(arg.file_path) + 
							"&q=" + encodeURIComponent(q);
				}
				else {
					u = "/cgi-bin/internalsearch.php?q=" + encodeURIComponent(q);
				}
				break;
			
			case "file_tab":
				var sel = $(".file_rdo[checked=true]");
				if (sel.length == 0) {
					alert(tinyMCEPopup.getLang('ooki_internalsearch_dlg.SelectFile')); //請選擇一個檔案
					return false;
				}
				u = sel.attr("m_sURL");
				q = sel.attr("m_sTitle");
				break;
		}
		
		if (focusElm && focusElm.nodeName == "A") 
		{
			tinyMCEPopup.execCommand("mceBeginUndoLevel");
			ed.focus();
			dom.setAttrib(focusElm, 'title', q);
			dom.setAttrib(focusElm, 'href', u);
			tinyMCEPopup.execCommand("mceEndUndoLevel");
		} 
		else
		{
			var h = '<a href="'+ u +'" title="'+ q +'">'+ selText +'</a>';
			ed.execCommand('mceInsertContent', false, h);
		}
		
		t.OnBnClose();
	},

	getAttrib : function(e, at) {
		var ed = tinyMCEPopup.editor, dom = ed.dom, v, v2;

		if (ed.settings.inline_styles) {
			switch (at) {
				case 'align':
					if (v = dom.getStyle(e, 'float'))
						return v;

					if (v = dom.getStyle(e, 'vertical-align'))
						return v;

					break;

				case 'hspace':
					v = dom.getStyle(e, 'margin-left')
					v2 = dom.getStyle(e, 'margin-right');

					if (v && v == v2)
						return parseInt(v.replace(/[^0-9]/g, ''));

					break;

				case 'vspace':
					v = dom.getStyle(e, 'margin-top')
					v2 = dom.getStyle(e, 'margin-bottom');
					if (v && v == v2)
						return parseInt(v.replace(/[^0-9]/g, ''));

					break;

				case 'border':
					v = 0;

					tinymce.each(['top', 'right', 'bottom', 'left'], function(sv) {
						sv = dom.getStyle(e, 'border-' + sv + '-width');

						// False or not the same as prev
						if (!sv || (sv != v && v !== 0)) {
							v = 0;
							return false;
						}

						if (sv)
							v = sv;
					});

					if (v)
						return parseInt(v.replace(/[^0-9]/g, ''));

					break;
			}
		}

		if (v = dom.getAttrib(e, at))
			return v;

		return '';
	},
	
	displayTab : function(tab_id, panel_id) {
		var t = this;
		t.g_tabSel = tab_id;
		mcTabs.displayTab(tab_id,panel_id);
		
		if ( tab_id == 'file_tab' )
			t.OnBnSearch();
	},
	
	OnBnSearch : function(arg) {
		var t=this, f, request;
		
		if (t.target_mode == "rs") {
			t.search_p = 1;
			t.OnBnSearch_RS();
		}
		else {
			f = document.forms[0];
			request = (arg ? arg : "q=" + encodeURIComponent(f.q.value));
			$.ajax({
				type: "GET"
			,	url: "/cgi-bin/internalSearch_mce.php?" + request
			,	cache: false
			,	success: function(r) {
					$("#file_Result").html(r);
					
					$("#file_Result .aC a").click( function() {
						var oA = $(this);
						InternalSearchDialog.OnBnSearch( InternalSearchDialog._B_URL_GetArg(oA.attr("href")) );
						return false;
					});
				}
			});
		}
	},
	
	OnBnSearch_RS : function() {
		var t=this, u, q, arg, request, data, dataJSON;
		arg = t.target_arg;
		u = "http://" + arg.host + "/tools/pv/pv_tools.php";
		q = $("#file_panel #q").val();
		request = "mode=search"
					+ "&act=all"
					+ "&site=" 	+ arg.site
					+ "&file_path=" + arg.file_path
					+ "&q=" 	+ decodeURIComponent( q )
//					+ "&tag=" 	+ decodeURIComponent( gArg.tag )
//					+ "&type=" 	+ decodeURIComponent( gArg.type )
//					+ "&fe=" 	+ decodeURIComponent( gArg.fe )
//					+ "&sort=" 	+ gArg.sort
//					+ "&order=" + gArg.order
					+ (t.search_p && t.search_p > 1 ? "&p=" + t.search_p : "")
					+ (t.search_ps && t.search_ps >= 5 ? "&ps=" + t.search_ps : "")
					+ "&out=rec"
					;
		data = external.AE_Msg("file_search", request);
		try { dataJSON = eval("(" + data + ")"); }
		catch(e) { alert( "Error: " + data ); }
		
		$("#file_Result").html( t.con_Recs2Items_RS(dataJSON, (t.search_p-1)*t.search_ps+1) + t.page_getHtml(t.search_p, t.search_ps, dataJSON.cnt) )
						.scrollTop(0);
	},
	
	con_Recs2Items_RS : function(data, px) {
		var t=this, h, recs, arg;
		recs = data.recs_info;
		if (!recs) return "";
		arg = t.target_arg;
		u_head = "http://" + arg.host;
		h = "";
		for (n in recs) {
			u = u_head + recs[n]['link'];
			h += '<table cellspacing="0" cellpadding="2" width="100%">'
				+	'<tr>'
				+		'<td valign="top" rowspan="4" width="1%">'
				+			'<input style="border:#000000 0px solid;" class="file_rdo" type="radio" name="file_rdo" m_sURL="' + u + '" m_sTitle="' + recs[n]['title'] + '"></td>'
				+		'<td width="1%">' + px + '.</td>'
				+		'<td><a style="color: #0000ff" target="_blank" href="' + u + '">' + recs[n]['title'] + '</a> ' + recs[n]['v_size'] + '</td>'
				+	'</tr>'
				+	'<tr><td colspan="2"></td>' + recs[n]['description'] + '</tr>'
				+	'<tr><td style="color: green" colspan="2">' + recs[n]['v_path'] + '</td></tr>'
				+'</table>'
				;
			px++;
		}
		return h;
	},
	
	OnBnPage : function(p) {
		var t=this;
		t.search_p = p;
		t.OnBnSearch_RS();
	},
	
	page_getHtml : function(p, ps, cnt) {
		var h;
		pc = ((cnt-1)/ps)+1;
		if (pc < 2) return "";
		pd = ps / 2; // 中間值
		if (p < 1) p = 1;
		
		h = '<div class="se_pages">';
		if (p > 1) h += '<a href="#" class="btn_s" onclick="InternalSearchDialog.OnBnPage(' + (p-1) + '); return false;">' + '上一頁' + '</a>'; // 上一頁
		
		x = (p > pd ? p -pd : 1);
		for (l=ps; l>0 && x<=pc; l--, x++) 
		{
			if (x == p)
				h += '<span class="btn_sel">' + x + '</span>';
			else
				h += '<a href="#" class="btn_n" onclick="InternalSearchDialog.OnBnPage(' + x + '); return false;">' + x + '</a>';
		}
		
		if (p < pc)
			h += '<a href="#" class="btn_s" onclick="InternalSearchDialog.OnBnPage(' + (p+1) + '); return false;">' + '下一頁' + '</a>'; // 下一頁
		h += '</div>';
		return h;
	},
	
	OnBnClose : function() {
		tinyMCEPopup.close();
	},
	
	_B_URL_GetArg : function(u, k) {
		var x = u.indexOf('?')+1;
		if ( !k ) {
			if ( x > -1 )
				return u.substr(x);
			else
				return u;
		}
		
		k += "=";
		if (u.substring(x,x+k.length) != k) {
			k = "&"+k;
			x = u.indexOf(k,x);
		}
		if (x == -1) return "";
		
		x += k.length;
		var y = u.indexOf('&',x);
		if (y == -1) y = u.length;
		return u.substring(x,y);
	}
	
};

InternalSearchDialog.preInit();
tinyMCEPopup.onInit.add(InternalSearchDialog.init, InternalSearchDialog);

