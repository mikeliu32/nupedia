var data_web = 
'goolgle	web	http://www.google.com.tw/search?hl=zh-TW&q=<keyword>&meta=lang_zh-TW|lang_zh-CN\n' + 
'goolgle	news	http://news.google.com.tw/news?hl=zh-TW&q=<keyword>&sa=N&tab=wn\n' + 
'goolgle	img	http://images.google.com.tw/images?q=<keyword>&hl=zh-TW\n' + 
'goolgle	blog	http://blogsearch.google.com/blogsearch?hl=en&q=<keyword>\n' + 
'goolgle	ec	http://local.google.com/froogle?q=<keyword>\n' + 
'goolgle	scr	http://scholar.google.com.tw/scholar?q=<keyword>&hl=zh-TW&lr=&lr=\n' + 

'yahoo	web	http://tw.search.yahoo.com/search?p=<keyword>&ei=UTF-8&fr=sfp&fl=0&x=wrt&vl=lang_zh-CN&vl=lang_zh-TW\n' + 
'yahoo	news	http://tw.search.yahoo.com/search/news?p=<keyword>&ei=UTF-8&fl=1&vl=lang_zh-CN&fr=fp-tab-img-t\n' + 
'yahoo	img	http://tw.search.yahoo.com/search/images?fr=fp-tab-img-t&p=<keyword>&ei=utf-8\n' + 
'yahoo	vdo	http://tw.videos.search.yahoo.com/search/video?fr=yfp&ei=UTF-8&p=<keyword>\n' + 
'yahoo	ec	http://tw.search.yahoo.com/search/products?p=<keyword>&ei=UTF-8&fl=1&vl=lang_zh-CN&fr=fp-tab-img-t&cop=mss\n' + 

'msn	web	http://search.msn.com.tw/results.aspx?q=<keyword>&FORM=QBRE&custom=1&checkcustom=1\n' + 
'msn	news	http://search.msn.com.tw/news/results.aspx?FORM=NRRE&q=<keyword>\n' + 
'msn	img	http://search.msn.com/images/results.aspx?q=<keyword>&FORM=MSTWH1&mkt=zh-TW\n' + 

'ask	web	http://web.ask.com/web?q=<keyword>&o=0&qsrc=undefined\n' + 
'ask	news	http://news.ask.com/news?qsrc=31&o=0&q=<keyword>&news=true\n' + 
'ask	img	http://pictures.ask.com/pictures?qsrc=1&o=0&q=<keyword>\n' + 
'ask	ec	http://ask2.pricegrabber.com/search_gen_top.php?topcat_search=1&form_keyword=<keyword>&qsrc=28&o=0\n' + 

'baidu	web	http://www.baidu.com/s?wd=<keyword>&cl=3&ie=utf-8\n' + 
'baidu	news	http://news.baidu.com/ns?cl=2&rn=20&tn=news&word=<keyword>&ie=utf-8\n' + 
'baidu	img	http://image.baidu.com/i?tn=baiduimage&ct=201326592&cl=2&lm=-1&rn=16&word=<keyword>&z=0&ie=utf-8\n' + 

'answers	dict	http://www.answers.com/<keyword>\n' + 
'answers	web	http://www.answers.com/<keyword>?web.x=1\n' + 
'answers	ec	http://www.answers.com/<keyword>?shop.x=1\n' + 

'Wikipedia	wiki	http://zh.wikipedia.org/wiki/<keyword>\n' + 

'flickr	img	http://www.flickr.com/search/?q=<keyword>\n' + 

'YouTube	vdo	http://tw.youtube.com/results?search_query=<keyword>&search_type=&aq=f\n';


var QuestDialog = {
	preInit : function() {
		var url;

		tinyMCEPopup.requireLangPack();

		if (url = tinyMCEPopup.getParam("external_image_list_url"))
			document.write('<script language="javascript" type="text/javascript" src="' + tinyMCEPopup.editor.documentBaseURI.toAbsolute(url) + '"></script>');
	},

	init : function(ed) {
		var t = this, ed = tinyMCEPopup.editor, n = ed.selection.getNode();
		
		t.m_bSelObj = false;
		t.m_selText = "";
		
		tinyMCEPopup.resizeToInnerSize();
		
		$( 'body' ).css('overflow', 'hidden' ).height( '100%' );
		$( 'body' ).attr('scroll', 'no');
		
		$('#insert').click( function(){t.insert();} );
		$('#cancel').click( function(){t.OnBnClose();} );
		
		if ( n.nodeName == "A" ) {
			t.m_bSelObj = true;
			t.m_selText = n.title == "" ? n.innerText : n.title;
		}
		else {
			t.m_selText = tinyMCE.trim(ed.selection.getContent({format:'text'}));
			// if ( t.m_selText == "" ) {
				// t.m_bSelObj = true;
				// t.m_selText = n.innerText;
			// }
		}
		
		$('#txtKey').val(t.m_selText);
		
		var data, out, name, type;
		out = '<table border="0" cellpadding="4" cellspacing="0" width="100%">';
		data = t._parseData();
		for ( name in data ) {
			out += '<tr>' + 
						'<td width="1%" align="right" valign="top" nowrap style="padding-top:8;">' + name + ' : ' + '</td>' + 
						'<td>';
			for ( type in data[name] )
				out += ' <input type="radio" id="rdo" name="rdo" value="' + data[name][type] + '" class="input_noborder" > ' + tinyMCEPopup.getLang('ooki_quest.' + type);
			
			out += '</td></tr>';
		}
		out += '</table>';
		$('#seItems').html(out);
		
	},

	_parseData : function() {
		var arow, acol, out;
		
		out = {};
		arow = data_web.split("\n");
		for (row=0; row<arow.length; row++) {
			if ( arow[row] == "" ) continue;
			acol = arow[row].split("\t");
			if ( acol.length < 3 ) continue;
			
			if ( !out[acol[0]] ) out[acol[0]] = {};
			out[acol[0]][acol[1]] = acol[2];
		}
		return out;
	},
	
	insert : function() {
		var t = this, ed = tinyMCEPopup.editor, dom = ed.dom, n = ed.selection.getNode();
		var oSel, oI, u, n, h, body, k;
		
		oSel = $('input').filter(":checked");
		if ( oSel.length > 0 ) {
			k = $('#txtKey').val();
			u = oSel.val();
			u = u.replace('<keyword>', encodeURIComponent(k));
			if ( t.m_bSelObj ) {
				tinyMCEPopup.execCommand("mceBeginUndoLevel");
				ed.focus();
				dom.setAttrib(n, 'title', k);
				dom.setAttrib(n, 'href', u);
				tinyMCEPopup.execCommand("mceEndUndoLevel");
			}
			else {
				h = '<a href="' + u + '" title="' + k + '">' + t.m_selText + '</a>';
				ed.execCommand('mceInsertContent', false, h);
			}
			
			t.OnBnClose();
		}
		else {
			alert(tinyMCEPopup.getLang('ooki_quest.please_selectlink'));
		}
	},

	OnBnClose : function() {
		tinyMCEPopup.close();
	}
};

QuestDialog.preInit();
tinyMCEPopup.onInit.add(QuestDialog.init, QuestDialog);
