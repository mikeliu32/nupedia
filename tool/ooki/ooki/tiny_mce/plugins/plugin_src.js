/////////////////////////////////////////////////////////////////
// 在 include 的程式必須設定以下參數
// 暫時儲存
var tempcontentTM_sKey = tempcontentTM_sKey || ""; // 設定一個唯一的 key
var tempcontentTM_type = tempcontentTM_type || ""; //分類

/*** 編輯器 Init call 外部使用 ***/
// function ooki_MCE_Setup_External(ed)

/*** 文章內容有變動時 call 給外部使用 ***/
// var tempcontentTM_OnDataChange_External = null;

/** 文章內容有變動時 call 外部使用 ***/
// function ooki_OnDataChange(ed, l)

/*** 暫時儲存, 取得文章內容; return => [T,D,C] ***/
// function ooki_temp_getData()

/*** 最近寫的文章, 回復之前的版本; rec => [t,T,D,C] ***/
// function ooki_last_OnDataRecover(rec)


/////////////////////////////////////////////////////////////////
var ooki_bCXHtmlView = false;
try {external.B_GetVersion(); ooki_bCXHtmlView = true;} catch(e){}

var browserTM = "";
var normal_node = Array("strong", "b", "strike", "p", "sub", "font");
var ooki_ObjStyle= 'border:1px dotted #cc0000; background-color:#ffffcc; padding:3px 10px;';
var mce_wc3 = false;
var ooki_user_css_info = {};
var ooki_target_mode = "";
var ooki_BaseURL = B_GetBaseURL("plugin_src.js");

document.write( '<script type="text/javascript" src="'+ooki_BaseURL+'ooki_abstract/abstractTM.js"></script>' );


var sys_debug = sys_debug || false;


if(document.all)
{
	browserTM = "ie";
}
else
{
	browserTM = "ff";
}

function displayOptTM(opt, mask, pos)
{
	opt.style.top = (pos.y + 100).toString() + "px";
	opt.style.left = (pos.x + 100).toString() + "px";
	mask.style.width = (pos.x + 1000).toString() + "px";
	mask.style.height = (pos.y + 1000).toString() + "px";
	opt.style.display = "block";
	mask.style.display = "block";
}

function replaceKeywordTM(link_v, term, mode)
{
	if(mode == 0)
	{
		link_v = link_v.replace(/<keyword>/g, encodeURIComponent(term));
	}
	else
	{
		link_v = link_v.replace(/<keyword>/g, term);
	}
	return link_v;
}

function str_covTM(str, mode)
{
        if(mode == 0)
        {
                var tmp = str.replace(/&quot;/gi,'"');
                tmp = tmp.replace(/&#039;/gi,"'");
                tmp = tmp.replace(/&lt;/gi,"<");
                tmp = tmp.replace(/&gt;/gi,">");
                tmp = tmp.replace(/&amp;/gi,"&");
        }
        else if(mode == 1)
        {
                var tmp = str.replace(/&/gi,"&amp;");
                tmp = tmp.replace(/"/gi,"&quot;");
                tmp = tmp.replace(/'/gi,"&#039;");
                tmp = tmp.replace(/</gi,"&lt;");
                tmp = tmp.replace(/>/gi,"&gt;");
        }
        else
        {
                var tmp = str.replace(/&/gi,"&amp;");
                tmp = tmp.replace(/</gi,"&lt;");
                tmp = tmp.replace(/>/gi,"&gt;");
        }

        return tmp;
}

function getOffsetTM(obj)
{
	var x = 0;
	var y = 0;
	while(obj)
	{
		x += obj.offsetLeft;
		y += obj.offsetTop;
		obj = obj.offsetParent;
	}
	return {x: x, y: y};
}

function get_ajaxTM()
{
	if(window.ActiveXObject)
	{
		try
		{
			return new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch(e)
		{
			try
			{
				return new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch(e2)
			{
				return null;
			}
		}
	}
	else if(window.XMLHttpRequest)
	{
		return new XMLHttpRequest();
	}
	else
	{
		return null;
	}
}

function ajax_sendTM(ajax, url, feedback)
{
	if(ajax)
	{
		var time = new Date();
		var now = time.getTime();
		url += "&useless=" + now;
		ajax.onreadystatechange = feedback;
		ajax.open("GET", url, true);
		ajax.send(null);
	}
}

function check_state1(editor_id, node, class_name, node_name, any_selection)
{
	//var inst = tinyMCE.getInstanceById(editor_id);
	//var sh = inst.selection.getSelectedHTML();
	//var st = inst.selection.getSelectedText();

	do{
		if(node.className.indexOf(class_name) != -1 && node.nodeName.toLowerCase() == node_name)
		{
			return "mceButtonSelected";
		}
		if(node.nodeName.toLowerCase() == "body")
		{
			break;
		}
	}while(node = node.parentNode);

	//var rule = /(?:class|CLASS|Class)=['"]?[^T]+TM\d?['"]?/;
	//if(any_selection && sh && !sh.match(rule))
	if(any_selection)
	{
		return "mceButtonNormal";
	}

	return "mceButtonDisabled";
}

function trace_backTM(focus_elm, class_name, node_name)
{
	do{
		if(focus_elm.nodeName.toLowerCase() == node_name &&
				focus_elm.className.indexOf(class_name) != -1)
		{
			break;
		}
	}while(focus_elm = focus_elm.parentNode);

	return focus_elm
}

function check_editorTM(editor_id)
{
	var inst = tinyMCE.getInstanceById(editor_id);
	var hidden_editor = tinyMCE.getInstanceById("annotationHiddenEditorTM");
	if(inst == hidden_editor)
	{
		return true;
	}
	hidden_editor = tinyMCE.getInstanceById("hidecontentHiddenEditorTM");
	if(inst == hidden_editor)
	{
		return true;
	}

	return false;
}



function B_IsUrl(u) 
{
	return u.substring(0,7).toLowerCase() == "http://";
}
	
function B_URL_InsertPath(u, Path) 
{
	if (B_IsUrl(u)) 
		return u;
	else if (u.substr(0,1) == '/')
		Path = B_URL_MakeIPPort(Path, true, false);
	else {
		if (Path.substr(Path.length-1,1) != '/') Path += '/';
	}
	return Path + u;
}

function B_URL_MakeIPPort(u, bHttp, bRootR)
{
	if (!B_IsUrl(u)) return "";
		
	var x = 0;
	if (bHttp)
		x = 7;
	else
		u = u.substring(7,u.length);
	
	x = u.indexOf('/',x);
	if (x > -1) {
		if (bRootR) x++;
		u = u.substring(0,x);
	}
	return u;
}

function B_URL_MakeFileName(u)
{
	x = u.indexOf("?");
	if (x > -1) u = u.substr(0,x);
	x = u.lastIndexOf("/");
	if (x > -1) u = u.substring(x+1,u.length);
	x = u.lastIndexOf("\\");
	if (x > -1) u = u.substring(x+1,u.length);
	return u;
}

function B_URL_MakeExtension(u)
{
	x = u.indexOf("?");
	if (x > -1) u = u.substr(0,x);
	x = u.lastIndexOf(".");
	if (x > -1) u = u.substring(x+1, u.length);
	return u;
}

function B_ParentId(o,id)
{
	while (o && o.id != id)
		o = o.parentNode;
	return o;
}

function B_con_String2Html(s)
{
	return !s ? "" : s.replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/\"/g,'&quot;');
}
function B_con_rectime2second(n)
{
	if (typeof(n) != "string" || n.length != 14)
		return 0;
	var t = new Date(n.substr(0,4)+"/"+n.substr(4,2)+"/"+n.substr(6,2)
				+" "+n.substr(8,2)+":"+n.substr(10,2)+":"+n.substr(12,2));
	return t.getTime()/1000;
}
function B_con_rectime2html(n, type)
{
	if (typeof(n) == "string" && n.length == 14)
	{
		// yyy/mm/dd hh:mm:ss
		if (type == 1)
		{
			return n.substr(0,4)+"/"+n.substr(4,2)+"/"+n.substr(6,2)
					+" "+n.substr(8,2)+":"+n.substr(10,2)+":"+n.substr(12,2);
		}
		// xx前
		else if (type == 2)
		{
			var t = new Date(n.substr(0,4)+"/"+n.substr(4,2)+"/"+n.substr(6,2)
					+" "+n.substr(8,2)+":"+n.substr(10,2)+":"+n.substr(12,2));
			var tCur = new Date();
			var tDiff = (tCur.getTime() - t.getTime()) / 1000;
			if (tDiff < 60)
				return tDiff+" 秒前";
			else if (tDiff < 3600)		// 1小時
				return parseInt(tDiff/60)+" 分鐘前";
			else if (tDiff < 86400)		// 1天
				return parseInt(tDiff/3600)+" 小時前";
			else if (tDiff < 604800)	// 7天
				return parseInt(tDiff/86400)+" 天前";
			else if (t.getFullYear() == tCur.getFullYear())
				return (t.getMonth()+1)+"/"+n.substr(6,2);
			else
				return n.substr(0,4)+"/"+n.substr(4,2)+"/"+n.substr(6,2);
		}
		// 1. yyy/mm/dd 
		// 2. hh:mm
		else
		{
			var t = new Date(n.substr(0,4)+"/"+n.substr(4,2)+"/"+n.substr(6,2)
					+" "+n.substr(8,2)+":"+n.substr(10,2)+":"+n.substr(12,2));
			var tCur = new Date();
			if (t.getFullYear() == tCur.getFullYear()
				&& t.getMonth() == tCur.getMonth()
				&& t.getDate() == tCur.getDate())
				return n.substr(8,2)+":"+n.substr(10,2);
				//return n.substr(8,2)+":"+n.substr(10,2)+":"+n.substr(12,2);
			else
				return n.substr(0,4)+"/"+n.substr(4,2)+"/"+n.substr(6,2);
		}
	}
	else
		return n || "";
}

function B_Object2String(o, type, level) {
	var sR="", sC="", sL="", ss="", Y, v, l;
	if (type == "undefined" || type == 0) {
		sR = "<br>";
		sC = "&nbsp;&nbsp;&nbsp;&nbsp;";
	}
	else {
		sR = "\r\n";
		sC = "\t";
	}
	
	if (typeof(level) != "number") level = 0;
	l = level++;
	while(l-- > 0) sL += sC;
	
	for (n in o) {
		if (n.indexOf("-") > -1)
			k = "\"" + n + "\"";
		else
			k = n;
		
		Y = typeof(o[n]);
		switch(Y) {
		case "object":
			if ( level > 1 ) continue;
			v = "{" + B_Object2String(o[n], type, level) + "}";
			break;
			
		case "number":
		case "boolean":
			v = o[n];
			break;
		
		case "string":
			v = '"' + o[n] + '"';
			break;
		
		default:
			continue;
			// ss += '(' + Y + ')';
			break;
		}
		ss += sR + sC + k + "(" + Y + ") : " + v + ", ";
	}
	
	if (ss.length) ss = ss.substr(0,ss.length-1);
	return ss;
}

function B_CheckError_SendResult(r, bShowErr, name)
{
	if (!r) return true;
	var br = true;
	if ( typeof(r) == "object" ) {
		br = r.error ? true : false;
		if (br && bShowErr) B_Message((name && name != "" ? "("+name+")" : "") + r.error);
	}
	else if ( typeof(r) == "string" ) {
		if (r.length > 128) r = r.substr(0,128);
		re = new RegExp("error:|<b>Warning</b>:|<b>Fatal error</b>:|<b>Notice</b>:|<b>Parse error</b>:", "i");
		br = re.test(r);
		if (br && bShowErr) B_Message((name && name != "" ? "("+name+")" : "") + r);
	}
	return br;
}

function B_LoadScrip(url)
{
	var elm = tinyMCE.DOM.create('script', {
		type : 'text/javascript',
		src : url
	});
	(document.getElementsByTagName('head')[0] || document.body).appendChild(elm);
}

function B_GetBaseURL(JSFileName)
{
	var elements = document.getElementsByTagName('script');
	var baseURL = "";
	var baseHREF = "";

	// If base element found, add that infront of baseURL
	nl = document.getElementsByTagName('base');
	for (i=0; i<nl.length; i++) {
		if (nl[i].href)
			baseHREF = nl[i].href;
	}

	for (var i=0; i<elements.length; i++) {
		if (elements[i].src && elements[i].src.indexOf(JSFileName) != -1) {
			var src = elements[i].src;
			src = src.substring(0, src.lastIndexOf('/')+1);

			// Force it absolute if page has a base href
			if (baseHREF != "" && src.indexOf('://') == -1)
				baseURL = baseHREF + src;
			else
				baseURL = src;
			break;
		}
	}
	return baseURL;
}

function B_HTMLEnCode(str)
{   
	var    s    =    "";   
	if    (str.length    ==    0)    return    "";   
	s    =    str.replace(/&/g,    "&gt;");   
	s    =    s.replace(/</g,        "&lt;");   
	s    =    s.replace(/>/g,        "&gt;");   
	s    =    s.replace(/    /g,        "&nbsp;");   
	s    =    s.replace(/\'/g,      "&#39;");   
	s    =    s.replace(/\"/g,      "&quot;");   
	s    =    s.replace(/\n/g,      "<br>");   
	return    s;   
}




// Store selection
function ooki_MCE_InsertContent(h) 
{
	if (!h || h.length == 0) return;
	tinyMCE.execCommand("mceInsertContent", false, h);
	// <img class="mceItemMedia mceItemWindowsMedia" 
		// src="http://localhost:5561/tools/ooki/tiny_mce/themes/advanced/img/trans.gif" 
		// width="320" height="240" 
		// data-mce-src="http://localhost:5561/tools/ooki/tiny_mce/themes/advanced/img/trans.gif" 
		// data-mce-json="{'type':'windowsmedia','video':{'sources':[]},'params':{'src':'http://localhost:5561/cgi-bin/aaa.mp4'},'width':'320','height':'240','class':'mceItemMedia mceItemFlash'}">
	$(tinyMCE.activeEditor.getDoc()).find("img").each(function(){
		var $t=$(this);
		if (/mceItemFlash|mceItemWindowsMedia/.test($t.attr('title')) && !$t.attr('data-mce-json')) {
			arg = tinymce.util.JSON.parse($t.attr('title').replace("class", "cla"));
			if (!arg) {
				alert("Error: Parameter error.");
				$t.remove();
				return;
			}
			cla = arg['class'];
			type = (cla == "mceItemWindowsMedia" ? "windowsmedia" : "flash");
			src = arg['src'];
			w = (arg['width'] || $t.width());
			h = (arg['height'] || $t.height());
			json = "{'type':'"+type+"','video':{'sources':[]},'params':{'src':'"+src+"'},'width':'"+w+"','height':'"+h+"','class':'mceItemMedia "+cla+"'}";
		
			$t.addClass('mceItemMedia');
			$t.addClass(cla);
			$t.attr('data-mce-src', src);
			$t.attr('data-mce-json', json);
		}
	})

	// 貼上的圖片不知道大小, 用 java 限制大小
	$img = $(tinyMCE.activeEditor.getDoc()).find("img.ooki_img_size_none");
	if ($img.length) {
		var size = $img.eq(0).attr("arg");
		$img.each(function(){
			$(this)	.removeClass("ooki_img_size_none")
					.removeAttr("arg");
		});
		if (size >= 40) ooki_img_LimiSize($img, {w:size, h:size});
	}
}


var ooki_store_inst = null;
function ooki_Selection_Store()
{
	ooki_store_inst = tinyMCE.selectedInstance;
	ooki_store_inst.selectionBookmark = ooki_store_inst.selection.getBookmark(true);
}
function ooki_Selection_Restore()
{
	tinyMCE.selectedInstance = ooki_store_inst;
	ooki_store_inst.getWin().focus();
	if (ooki_store_inst.selectionBookmark) {
		ooki_store_inst.selection.moveToBookmark(ooki_store_inst.selectionBookmark);
	}
}

// Popup Window 框
function ooki_Popup_Open(c)
{
	var dlg, o, o2, offset;
	if ( mce_wc3 ) {
		dlg = 	'<div class="ookiPopupMask" style="height:' + $(document).height() + 'px;"></div>' + 
				'<table class="ookiPopup" border="0" style="top:' + $(document).scrollTop() + 'px; height:' + $(window).height() + 'px;"><tr><td>' + 
					c + 
				'</td></tr></table>';
		
		$('body').append( dlg );
		window.setTimeout(ooki_Popup_Open_ResetSize, 500);
	}
	else
	{
		dlg = 	'<div class="ookiPopupMask" style="height:' + $(document).height() + 'px;"></div>'
			+	'<table class="ookiPopup" border="0" style="top:' + $(document).scrollTop() + 'px; height:100%;"><tr><td>'
			+		c
			+	'</td></tr></table>';
		$('body').append( dlg );
	}
}

function ooki_Popup_Open_ResetSize()
{
	if ( $('.ookiPopup .ookiPopupIn').width() < $('.ookiPopup .mceLayout').width() +5 )
		$('.ookiPopup .ookiPopupIn').width( $('.ookiPopup .mceLayout').width() +5 );
	$('.ookiPopup .ookiPopupIn').height( "" );
}

function ooki_Popup_Close()
{
	$(".ookiPopupMask").remove();
	$(".ookiPopupMask2").remove();
	$(".ookiPopup").remove();
}

// 限制圖片大小
// $os: 那些物件
// ls: {w:xx, h:xx}限制大小
function ooki_img_LimiSize($os, ls)
{
	$os	.load( function(){ooki_img_DoLimiSize($(this), ls);} )
		.each( function(){ooki_img_DoLimiSize($(this), ls);} );
}
function ooki_img_DoLimiSize($o, ls)
{
	var img=$o, ls_o;
	img.css({width:"auto", height:"auto"});
	if (ls.w > 0 && img.width() > 0) {
		ls_o = ooki_img_GetLimiSize(img.width(), img.height(), ls);
		if (ls_o.w > 0 && ls_o.h > 0)
			img.css({width:ls_o.w, height:ls_o.h});
	}
}
function ooki_img_GetLimiSize(w, h, ls)
{
	var nRate, win=$(window);
	if ( w > ls.w || h > ls.h ) {
		nRate = (ls.h > -1 ? min( ls.w / w, ls.h / h ) : ls.w / w);
		w = parseInt(w * nRate);
		h = parseInt(h * nRate);
		if ( w < 1 ) w = 1;
		if ( h < 1 ) h = 1;
		return {w:w, h:h};
	}
	return {w:0, h:0};
}
function min(a,b)
{
	return a < b ? a : b;
}


function ooki_content_aftDelSpace(s)
{
	return s.replace(/(<p>&nbsp;<\/p>\s*)*$/ig, "");
}

function ooki_icon_wait()
{
	return tinyMCE.baseURL + "/themes/advanced/img/wait.gif";
}

function ooki_getContent(eid)
{
	var ed = tinyMCE.get(eid);
	return ooki_content_aftDelSpace( ed.getContent() );
}

function ooki_getContent_ed(ed)
{
	return ooki_content_aftDelSpace( ed.getContent() );
}

function ooki_init(arg)
{
	if ( !tinyMCE.ooki ) tinyMCE.ooki = {};
	for (n in arg)
		tinyMCE.ooki[n] = arg[n];
	
	// meta init
	if ( !tinyMCE.ooki.meta ) tinyMCE.ooki.meta = {};
	if ( arg.author ) tinyMCE.ooki.meta['author'] = arg.author;
	if ( arg.title ) tinyMCE.ooki.meta['title'] = arg.title;
	
}

function ooki_quit()
{
}
// ooki 上傳之前
function ooki_save_before(funcOK)
{
	tempcontentTM_DatasSave(funcOK);
}
// ooki 上傳之後
function ooki_save_after(funcOK)
{
	ooki_api_temp({
		fun:	"2last",
		k:		tempcontentTM_sKey,
		funcOK:	function(data) {
			tempcontentTM_bChang = false;
			if (funcOK) funcOK(data);
		}
	});
}

function ooki_meta_getContent()
{
	var h="", hT="";
	if ( tinyMCE.ooki.meta ) {
		for (n in tinyMCE.ooki.meta) {
			if ( tinyMCE.ooki.meta[n].length ) {
				h += '<meta name="' + n + '" content="' + B_HTMLEnCode(tinyMCE.ooki.meta[n].replace(/\s+/g, " ")) + '">';
				// if ( n == "title" ) 
					// hT = '<title> ' + B_HTMLEnCode(tinyMCE.ooki.meta[n]) + ' </title>';
			}
		}
	}
	return hT + h;
}

function ooki_meta_setOnChange(func)
{
	tinyMCE.ooki.meta.onchange = func;
}

function ooki_editors_reload() 
{
	var eds, cnt, id;
	eds = tinyMCE.EditorManager.editors;
	cnt = eds.length;
	for (x=0; x<cnt; x++) {
		id = eds[x].id;
		tinyMCE.execCommand('mceRemoveControl', false, id);
		tinyMCE.execCommand('mceAddControl', false, id);
	}
}

function ooki_MCE_Setup(ed)
{
// 修正 "還原" and "重做" 沒有反應.
	ed.onKeyUp.add(function(ed, e) {
		// 還原 ctrl + z
		if ((e.keyCode == 112 || e.keyCode == 90) && e.ctrlKey) {
			setTimeout( function() {
				ed.undoManager.undo();
			}, 1);
		}
		// 重做 ctrl + y
		else if ((e.keyCode == 89) && e.ctrlKey) {
			setTimeout( function() {
				ed.undoManager.redo();
			}, 1);
		}
		// 貼上 ctrl + v
		else if (ooki_bCXHtmlView && (e.keyCode == 86) && e.ctrlKey) {
			setTimeout( function() {
				var data = external.AE_Msg("onPaste", "");
				ooki_MCE_InsertContent( data );
			}, 1);
		}
	});
	
	ed.onPreProcess.add(function(ed, o) {
		var dom = ed.dom;
		if (o.get) {
// 修正表格改變 size 沒有作用.
			$(o.node).find("table").attr("_mce_style", "");
		}
	});
	// 內容有變動
	ed.onChange.add(function(ed, l) {
		if (typeof(ooki_OnDataChange) == "function")
			ooki_OnDataChange(ed, l);
	});
		
		
/*
	ed.onKeyPress.add(function(ed, e) {
		if (e.keyCode == 13 && e.shiftKey && ed.selection.getNode().nodeName != 'LI') {
			var n;
			ed.selection.setContent('<p id="__" /></p>', {format : 'raw'});
			n = ed.dom.get('__');
			n.removeAttribute('id');
			ed.selection.select(n);
			ed.selection.collapse();
			
			e.preventDefault();
			e.stopPropagation();
			return false;
		}
	});
*/

	if (typeof(ooki_MCE_Setup_External) == "function")
		ooki_MCE_Setup_External(ed);

}
// 在編輯區插入 檔案
function ooki_edit_InsertObj(fn, u_fp)
{
	var img 	= ",jpg,gif,png,bmp,ico,jpeg,pcd,giif,tiff,";
	var flash 	= ",flv,mov,swf,";
	var video 	= ",avi,mpg,wmv,mpeg,mov,mp4,mts,m2ts,m2t,f4v,";
	var audio 	= ",mp3,wma,wav,ape,flac,acc,amr,ogg,";
	var h="", u_fp, ext;
	
	ext = B_URL_MakeExtension(fn).toLowerCase();
	// Image
	if (img.indexOf(','+ext+',') > -1) {
		h = '<img src=\"'+u_fp+'\" title="ooki_img_size_none"><br>';
	}
	// Flash
	else if (flash.indexOf(','+ext+',') > -1) {
		h = '<img class="mceItemFlash" title="{class:\'mceItemFlash\',src:\''+u_fp+'\',width:\'540\',height:\'405\',autostart:\'0\'}" width="540" height="405" src="/ooki/tiny_mce/themes/advanced/img/spacer.gif"><br>'
			+'<a id=attach_file href="'+u_fp+'">'+fn+'</a><br>';
	}
	// Video
	else if (video.indexOf(','+ext+',') > -1) {
		h = '<img class="mceItemWindowsMedia" title="{class:\'mceItemWindowsMedia\',src:\''+u_fp+'\',width:\'540\',height:\'405\',autostart:\'0\'}" width="540" height="405" src="/ooki/tiny_mce/themes/advanced/img/spacer.gif"><br>'
			+'<a id=attach_file href="'+u_fp+'">'+fn+'</a><br>';
	}
	// Audio
	else if (audio.indexOf(','+ext+',') > -1) {
		h = '<img class="mceItemWindowsMedia" title="{class:\'mceItemWindowsMedia\',src:\''+u_fp+'\',width:\'200\',height:\'45\',autostart:\'0\'}" width="200" height="45" src="/ooki/tiny_mce/themes/advanced/img/spacer.gif"><br>'
			+'<a id=attach_file href="'+u_fp+'">'+fn+'</a><br>';
	}
	else {
		h = '<a id=attach_file href="'+u_fp+'">'+fn+'</a><br>';
	}
	tinyMCE.execCommand("mceInsertContent", false, h);
	
	$(tinyMCE.activeEditor.getDoc()).find("img").each(function(){
		var $t=$(this);
		var title=$t.attr('title');
		// Video 沒有 data-mce-json 
		if (/mceItemFlash|mceItemWindowsMedia/.test(title) && !$t.attr('data-mce-json')) {
			arg = tinymce.util.JSON.parse(title);
			if (!arg) return;
			cla = arg['class'];
			type = (cla == "mceItemWindowsMedia" ? "windowsmedia" : "flash");
			src = arg['src'];
			w = (arg['width'] || $t.width());
			h = (arg['height'] || $t.height());
			json = "{'type':'"+type+"','video':{'sources':[]},'params':{'src':'"+src+"'},'width':'"+w+"','height':'"+h+"','class':'mceItemMedia "+cla+"'}";
		
			$t.addClass('mceItemMedia');
			$t.addClass(cla);
			$t.attr('data-mce-src', src);
			$t.attr('data-mce-json', json);
		}
		// Image 限制大小
		if (/ooki_img_size_none/.test(title)) {
			$t.attr('title', "");
			ooki_img_LimiSize($t, {w:680, h:510});
		}
	})
}




/////////////////////////////////////////////////////////////////
// Last Content
function rs_lastcontentTM_DataSave(arg)
{
	try {
		ooki_api_last({
			fun:	"save",
			T:		arg.T,
			D:		arg.D,
			C:		arg.C,
			funcOK:	function(data) {
				if (arg.funcOK) arg.funcOK(data);
			}
		});
	} catch(e) { if (arg.funcOK) arg.funcOK(); }
}
function rs_lastcontentTM_DataGet(arg)
{
	try {
		ooki_api_last({
			fun:	"get",
			t:		arg.t,
			funcOK:	function(data) {
				if (arg.funcOK) arg.funcOK(data);
			}
		});
	} catch(e) { if (arg.funcOK) arg.funcOK(); }
}
function rs_lastcontentTM_DataList(funcOK)
{
	try {
		ooki_api_last({
			fun:	"list",
			funcOK:	function(data) {
				if (funcOK) funcOK(data);
			}
		});
	} catch(e) { if (arg.funcOK) arg.funcOK(); }
}


function lastcontentTM_DataList()
{
	if (!ooki_bCXHtmlView) return;
	try{ return external.AE_B_LastContentList(); } 
	catch(e) {}
}
function lastcontentTM_DataGet(k)
{
	if (!ooki_bCXHtmlView) return;
	return external.AE_B_LastContentGet(k);
}
function lastcontentTM_DataSave(k,C)
{
	if (!ooki_bCXHtmlView) return;
	try{ external.AE_B_LastContentSave(k,C); } 
	catch(e) {}
}
function lastcontentTM_DatasSave(k)
{
	if (!ooki_bCXHtmlView) return;
	var eds, cnt, sC, sK;
	eds = tinyMCE.EditorManager.editors;
	cnt = eds.length;
	if (cnt > 1) {
		for (var x=0; x<cnt; x++) {
			sC = $.trim(ooki_getContent(eds[x].id));
			if (sC == "") continue;
			sK = k + "#" + x;
			external.AE_B_LastContentSave(sK, sC);
		}
	}
	else {
		sC = $.trim(ooki_getContent(eds[0].id));
		external.AE_B_LastContentSave(k, sC);
	}
}

/////////////////////////////////////////////////////////////////
// 暫時儲存
var tempcontentTM_bDataChange = false;
var tempcontentTM_bRecover = true;
var tempcontentTM_nTimeID = 0;
var tempcontentTM_o = new Object();
var tempcontentTM_bWin = true;
var tempcontentTM_bChang = null;

function tempcontentTM_init(eid, bWin)
{
	tempcontentTM_bWin = bWin != false;
	if (tempcontentTM_bWin) {
		tempcontentTM_o[eid] = new Object();
		tempcontentTM_o[eid].bChang = false;
	} else {
		if (!tempcontentTM_bChang) {
			tempcontentTM_bChang = false;
		}
	}
}
function tempcontentTM_DataChange(eid)
{
	tempcontentTM_bDataChange = true;
	if (tempcontentTM_nTimeID == 0) 
		tempcontentTM_nTimeID = setTimeout(tempcontentTM_DatasSave, 10000);
	
	if (tempcontentTM_bWin) {
		tempcontentTM_o[eid].bChang = true;
	} else {
		tempcontentTM_bChang = true;
	}
	if (typeof(tempcontentTM_OnDataChange_External) == "function")
		tempcontentTM_OnDataChange_External(eid);
}
function tempcontentTM_DatasSave(funcOK)
{
	if ( tempcontentTM_nTimeID ) clearTimeout( tempcontentTM_nTimeID );
	tempcontentTM_nTimeID = 0;
	
	var eds = tinyMCE.EditorManager.editors;
	if (tempcontentTM_bWin) {
		var cnt = eds.length;
		var nX = 0;
		do_save();
		function do_save() {
			// OK
			if (nX >= cnt) {
				if (funcOK) funcOK();
				return;
			}
			//
			tempcontentTM_DataSave(eds[nX].id, function(){
				nX++;
				do_save();
			});
		}
	}
	else {
		var rec = ooki_temp_getData()
		ooki_api_temp({
			fun:	"save",
			k:		tempcontentTM_sKey,
			T:		rec.T,
			D:		rec.D,
			C:		rec.C,
			funcOK:	function(data) {
				tempcontentTM_bChang = false
				if (funcOK) funcOK(data);
				//
				for(var x in eds)
					eds[x].controlManager.setDisabled('rs_tempcontent', true);
			}
		});
	}
}

function tempcontentTM_DataSave(eid, funcOK)
{
	if (tempcontentTM_bWin) {
		if (!tempcontentTM_o[eid] || !tempcontentTM_o[eid].bChang) {
			if (funcOK) funcOK("ok");
			return;
		}
		tempcontentTM_o[eid].bChang = false;
		
		try{
			var ed = tinyMCE.get( eid );
			var k = tempcontentTM_eid2k(eid);
			var v = ooki_getContent(eid);
			// Win
			if (tempcontentTM_bWin)
			{
				external.AE_B_TempContentSave(k, v);
				if (funcOK) funcOK("ok");
				//
				ed.controlManager.setDisabled('tempcontent', true);
			}
			// NUWebCS
			else
			{
				ooki_api_temp({
					fun:	"save",
					k:		k,
					v:		v,
					funcOK:	function(data) {
						if (funcOK) funcOK(data);
						//
						ed.controlManager.setDisabled('rs_tempcontent', true);
					}
				});
			}
		} catch(e) { if (funcOK) funcOK(); }
	}
	else {
		tempcontentTM_DatasSave(funcOK);
	}
}

function tempcontentTM_DataFind(eid, funcOK)
{
	var k = tempcontentTM_eid2k(eid)
	if (tempcontentTM_bWin) {
		var data = external.AE_B_TempContentGet(k);
		if (funcOK) funcOK(data);
	} else {
		ooki_api_temp({
			fun:	"get",
			k:		k,
			funcOK:	function(data) {
				if (funcOK) funcOK(data);
			}
		});
	}
}
function tempcontentTM_rs_DataFind(funcOK)
{
	ooki_api_temp({
		fun:	"get",
		k:		tempcontentTM_sKey,
		funcOK:	function(data) {
			if (funcOK) funcOK(data);
		}
	});
}
function tempcontentTM_rs_DataDelete(funcOK)
{
	ooki_api_temp({
		fun:	"del",
		k:		tempcontentTM_sKey,
		funcOK:	function(data) {
			if (funcOK) funcOK(data);
		}
	});
}


function tempcontentTM_DatasDelete(funcOK)
{
	var eds, cnt, nX;
	eds = tinyMCE.EditorManager.editors;
	cnt = eds.length;
	nX = 0;
	do_del();
	function do_del() {
		// OK
		if (nX >= cnt) {
			if (funcOK) funcOK();
			return;
		}
		//
		tempcontentTM_DataDelete(eds[nX].id, function(){
			nX++;
			do_del();
		});
	}
}

function tempcontentTM_DataDelete(eid, funcOK)
{
	try{
		var k = tempcontentTM_eid2k(eid)
		if (tempcontentTM_bWin) {
			external.AE_B_TempContentDelete(k);
			if (funcOK) funcOK(data);
		}
		else {
			ooki_api_temp({
				fun:	"del",
				k:		k,
				funcOK:	function(data) {
					if (funcOK) funcOK(data);
				}
			});
		}
	} 
	catch(e) {}
}

function tempcontentTM_eid2k(eid)
{
	return tempcontentTM_sKey + "#" + eid;
}



// {fun[save / get / del / 2last(轉到 last)], k, T, D, C]}
function ooki_api_temp(arg)
{
	try {
	var argData = 	
		"mode=ooki_temp_" + arg.fun
		+"&k=" + encodeURIComponent(arg.k||"")
		+"&T=" + encodeURIComponent(arg.T||"")
		+"&D=" + encodeURIComponent(arg.D||"")
		+"&C=" + encodeURIComponent(arg.C||"")
		+"&type=" + encodeURIComponent(tempcontentTM_type||"")
	$.ajax({
		type: "POST"
		,url: "/tools/api_user_info.php"
		,data: argData
		,dataType: "json"
		,success: function(data, textStatus) {
// console.log("*** /tools/api_user_info.php?"+argData);
// console.log(data);
			if (B_CheckError_SendResult(data, sys_debug)) {
				data = null;
			}
			if (arg.funcOK) arg.funcOK(data);
		}
	,	error: function(XMLHttpRequest, textStatus, errorThrown) {
// console.log("*** /tools/api_user_info.php?"+argData);
// console.log(XMLHttpRequest.responseText);
			if (sys_debug && B_Message) B_Message( "Error: (ooki_api_temp)" + XMLHttpRequest.status + ", " + XMLHttpRequest.responseText );
			if (arg.funcOK) arg.funcOK();
		}
	});
	} catch(e) {if (arg.funcOK) arg.funcOK();}
}
// {fun[save / get / del], k, T, D, C, funcOK}
function ooki_api_last(arg)
{
	try {
	var argData = 	
		"mode=ooki_last_" + arg.fun
		+(arg.t ? "&t=" + encodeURIComponent(arg.t) : "")
		+(arg.k ? "&k=" + encodeURIComponent(arg.k) : "")
		+(arg.T ? "&T=" + encodeURIComponent(arg.T) : "")
		+(arg.D ? "&D=" + encodeURIComponent(arg.D) : "")
		+(arg.C ? "&C=" + encodeURIComponent(arg.C) : "")
		+"&type=" + encodeURIComponent(tempcontentTM_type||"")
	$.ajax({
		type: "POST"
		,url: "/tools/api_user_info.php"
		,data: argData
		,dataType: "json"
		,success: function(data, textStatus) {
// console.log("*** /tools/api_user_info.php?"+argData);
// console.log(data);
			if (B_CheckError_SendResult(data, sys_debug))
				data = null;
			if (arg.funcOK) arg.funcOK(data);
		}
		,error: function(XMLHttpRequest, textStatus, errorThrown) {
// console.log("*** /tools/api_user_info.php?"+argData);
// console.log(XMLHttpRequest);
			if (sys_debug && B_Message) B_Message( "Error: (ooki_api_last)" + XMLHttpRequest.status + ", " + XMLHttpRequest.responseText );
			if (arg.funcOK) arg.funcOK();
		}
	});
	} catch(e) {if (arg.funcOK) arg.funcOK();}
}
