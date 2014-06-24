
var ooki_BaseURL = ooki_GetBaseURL('plugin_res.js');

/*** SyntaxHighlighter ***
	3. 名稱對應表如下 ( http://alexgorbatchev.com/SyntaxHighlighter/manual/brushes/ )
	
	語言			別名							檔名
	ActionScript3	as3, actionscript3				shBrushAS3.js
	Bash/shell		bash, shell						shBrushBash.js
	ColdFusion		cf, coldfusion					shBrushColdFusion.js
	C#				c-sharp, csharp					shBrushCSharp.js
	C++				cpp, c							shBrushCpp.js
	CSS				css								shBrushCss.js
	Delphi			delphi, pas, pascal				shBrushDelphi.js
	Diff			diff, patch						shBrushDiff.js
	Erlang			erl, erlang						shBrushErlang.js
	Groovy			groovy							shBrushGroovy.js
	JavaScript		js, jscript, javascript			shBrushJScript.js
	Java			java							shBrushJava.js
	JavaFX			jfx, javafx						shBrushJavaFX.js
	Perl			perl, pl						shBrushPerl.js
	PHP				php								shBrushPhp.js
	Plain Text		plain, text						shBrushPlain.js
	PowerShell		ps, powershell					shBrushPowerShell.js
	Python			py, python						shBrushPython.js
	Ruby			rails, ror, ruby				shBrushRuby.js
	Scala			scala							shBrushScala.js
	SQL				sql								shBrushSql.js
	Visual Basic	vb, vbnet						shBrushVb.js
	XML				xml, xhtml, xslt, html, xhtml	shBrushXml.js
*/
var syntax_highlighter_required =
	// JS files
	'<script type="text/javascript" src="' + 
	ooki_BaseURL + 'SyntaxHighlighter/XRegExp.js"></script>' +
	'<script type="text/javascript" src="' + 
	ooki_BaseURL + 'SyntaxHighlighter/shCore.js"></script>' + // core
	'<script type="text/javascript" src="' + 
	ooki_BaseURL + 'SyntaxHighlighter/shBrushJScript.js"></script>' + // js
	'<script type="text/javascript" src="' + 
	ooki_BaseURL + 'SyntaxHighlighter/shBrushCpp.js"></script>' + // cpp
	'<script type="text/javascript" src="' + 
	ooki_BaseURL + 'SyntaxHighlighter/shBrushPhp.js"></script>' + // php
	'<script type="text/javascript" src="' + 
	ooki_BaseURL + 'SyntaxHighlighter/shBrushCss.js"></script>' + // css
	// CSS files
	'<link rel="stylesheet" type="text/css" href="' +
	ooki_BaseURL + 'SyntaxHighlighter/shCore.css' + '"/>' +
	'<link rel="stylesheet" type="text/css" href="' +
	ooki_BaseURL + 'SyntaxHighlighter/shThemeDefault.css' + '"/>'
	;
/*** End of SyntaxHighlighter ***/

var ookiAnnotationTimeIdTM = null;


document.write('<script src="' + ooki_BaseURL + 'plugins/media/jscripts/embed.js"></script>');
document.write('<script src="/tools/flowplayer/flowplayer-3.2.8.min.js"></script>');
document.write(syntax_highlighter_required);

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

function annotationClAnTM(e)
{
	clearTimeout(ookiAnnotationTimeIdTM);
	annotatedTM(e);
}

function annotationClTimeTM()
{
	clearTimeout(ookiAnnotationTimeIdTM);
}

function annotationDeDanTM(e)
{
	clearTimeout(ookiAnnotationTimeIdTM);
	ookiAnnotationTimeIdTM = setTimeout(disannotatedTM, 500);
}

function annotatedTM(event)
{
	var insert = document.body;
	var annotated;

	if(!event)
		event = window.event;
	annotated = (event.srcElement) ? event.srcElement : event.target;

	while(annotated && annotated.className.toLowerCase() != "annotatedtm")
	{
		annotated = annotated.parentNode;
	}

	var offset = getOffsetTM(annotated);

	var annotation;
	if((annotation = document.getElementById("ookiAnnotationTM")))
	{
		document.body.removeChild(annotation);
	}

	var annotation = document.createElement('div');

	annotation.id = "ookiAnnotationTM";
	annotation.onmouseout = annotationDeDanTM;
	annotation.onmouseover = annotationClTimeTM;
	annotation.style.left = (offset.x) + "px";
	annotation.style.top = (offset.y + 30)  + "px";

	var tmp = annotated.getAttribute("annotationData");
	if (!tmp) tmp = annotated.getAttribute("annotationdata");
	while(tmp.indexOf("mce_thref") != -1)
	{
		tmp = tmp.replace("mce_thref","href");
	}
	annotation.innerHTML += decodeURIComponent(tmp);

	insert.appendChild(annotation);
}


function disannotatedTM()
{
	var par = document.body;
	var annotation = document.getElementById("ookiAnnotationTM");

	if(annotation != null)
	{
		par.removeChild(annotation);
	}
}

function ookiAnnotationClAnTM(o)
{
	clearTimeout(ookiAnnotationTimeIdTM);
	ookiAnnotatedTM(o);
}

function ookiAnnotationClTimeTM()
{
	clearTimeout(ookiAnnotationTimeIdTM);
}

function ookiAnnotationDeDanTM(o)
{
	clearTimeout(ookiAnnotationTimeIdTM);
	ookiAnnotationTimeIdTM = setTimeout(disannotatedTM, 500);
}


function ookiAnnotatedTM(o)
{
	var insert = document.body;
	var annotated;
	
	annotated = o.parentElement;
	var offset = getOffsetTM(annotated);
	//alert(offset.x + ", " + offset.y);

	var annotation;
	if((annotation = document.getElementById("ookiAnnotationTM")))
	{
		document.body.removeChild(annotation);
	}

	var annotation = document.createElement('div');
	annotation.id = "ookiAnnotationTM";
	annotation.onmouseover = ookiAnnotationClTimeTM;
	annotation.onmouseout = ookiAnnotationDeDanTM;
	annotation.style.left = (offset.x) + "px";
	annotation.style.top = (offset.y + 30)  + "px";
	annotation.innerHTML = annotated.children.content.innerHTML;

	insert.appendChild(annotation);

	// ookiAnnotated_Dlg_Close();
	// var C = annotated.children.content.innerHTML
	// var offset = $o.offset();
	// var h =
		// '<div id="ookiAnnotationTM"'
			// +' onmouseover="ookiAnnotationClTimeTM(this);"'
			// +' onmouseout="ookiAnnotationDeDanTM(this);"'
			// +' style="left:'+(offset.left)+'px; top:'+(offset.top + 30)+'px">'
			// + C
		// +'</div>'
	// $("body").append(h);
}

function ookiAnnotated_MouseOver(o)
{
	clearTimeout(ookiAnnotationTimeIdTM);
	ookiAnnotated_Dlg_Show(o);
}

function ookiAnnotated_MouseOut(o)
{
	clearTimeout(ookiAnnotationTimeIdTM);
	ookiAnnotationTimeIdTM = setTimeout(ookiAnnotated_Dlg_Close, 500);
}

function ookiAnnotated_Dlg_MouseOver(o)
{
	clearTimeout(ookiAnnotationTimeIdTM);
}

function ookiAnnotated_Dlg_Show(o)
{
	ookiAnnotated_Dlg_Close();
	
	var $o = $(o);
	var $oC = $("DIV#"+$o.attr("id"));
	var C = $oC.html();
	var offset = $o.offset();
	var h =
		'<div id="ookiAnnotationTM"'
			+' onmouseover="ookiAnnotated_Dlg_MouseOver(this);"'
			+' onmouseout="ookiAnnotated_MouseOut(this);"'
			+' style="left:'+(offset.left)+'px; top:'+(offset.top + 30)+'px">'
			+ C
		+'</div>'
	$("body").append(h);
}

function ookiAnnotated_Dlg_Close()
{
	$("#ookiAnnotationTM").remove();
}



function hidecontentTM(obj)
{
	var obj = obj.parentElement;
	if(obj)
	{
		var oSign = obj.children.title.children.sign;
		if (oSign) 
		{
			if(oSign.innerHTML == '▼')
			{
				oSign.innerHTML = '▲';
				if (obj.children.content.tagName == "DIV")
					obj.children.content.style.display = 'block';
				else
					obj.children.content.style.display = 'inline';
			}
			else
			{
				oSign.innerHTML = '▼';
				obj.children.content.style.display = 'none';
			}
		}
	
		// old
		else if (obj.all('sign')) 
		{
			if(obj.all('sign').innerHTML == '▼')
			{
				obj.all('sign').innerHTML = '▲';
				obj.all('content').style.display = 'block';
			}
			else
			{
				obj.all('sign').innerHTML = '▼';
				obj.all('content').style.display = 'none';
			}
		}
	}
}

function ookiHidecontent_Click(obj)
{
	var id = obj.id;
	if (id == "title")
	{
		var oI, oSign, oC;
		oI = obj.parentNode;
		oSign = obj.children.sign;
		if (oSign) 
		{
			if(oSign.innerHTML == '▼')
			{
				oSign.innerHTML = '▲';
				if ( oI.id ) {
					oC = B_getElementByIdTag(oI.id, "DIV");
					if ( oC ) oC.style.display = "block";
				}
				else {
					oI.all.content.style.display = 'block';
				}
			}
			else
			{
				oSign.innerHTML = '▼';
				if ( oI.id ) {
					oC = B_getElementByIdTag(oI.id, "DIV");
					if ( oC ) oC.style.display = "none";
				}
				else {
					oI.all.content.style.display = 'none';
				}
			}
		}
	}
}

function ooki_writeHideContent(p) {
	var h;
	h = '<span><span style="cursor:hand;" onclick="hidecontentTM(this);">' + p.title + '<span id=sign>▼</span></span>' + 
				'<span id=content style="display:none">' + decodeURIComponent(p.content) + '</span></span>';
	document.write(h);
}

// region server, 插入 TableMode
function ookiWriteRSDirTable(obj) {
	if (obj.src && obj.src.length > 0) {
		document.write('<div><iframe src="' + obj.src + '" id="ooki_ifTable" name="ooki_ifTable" onload="ookiWriteRSDirTableOK(this);" style="display:none;"></iframe></div>');
	}
}

function ookiWriteRSDirTableOK(o) {
	var oif = B_GetIFrame(o.name);
	if (oif) {
		var oDiv = o.parentElement;
		oDiv.innerHTML = oif.document.body.innerHTML;
		
		myRe = /<link [^>]*href="([^>]*)"/ig;
		myArray = myRe.exec(oif.document.body.innerHTML);
		if (myArray) {
			var o = document.createElement("link");
			o.href = myArray[1];
			o.type = "text/css";
			o.rel = "stylesheet";
			document.body.appendChild(o);
		}
	}
}

// 背景音樂 bgsound
var bgsoundTM_TimeID = 0;
function ookiBGSoundTM_Player(b)
{
	if (b) 
	{
		if (bgsoundTM_TimeID) {
			clearTimeout(bgsoundTM_TimeID);
			bgsoundTM_TimeID = 0;
		}
		bgsound_player.style.display = 'inline';
	}
	else 
	{
		if (bgsoundTM_TimeID) {
			bgsound_player.style.display = 'none';
			bgsoundTM_TimeID = 0;
		}
		else
			bgsoundTM_TimeID = setTimeout('ookiBGSoundTM_Player(false);', 1000);
	}
}

function ookiBGSoundTM(p, n)
{
	var u, sUrlPath, op;
	u = p.src;
	sUrlPath = B_URL_MakePath(document.location, true, true);
	h = '<div style="float:right; text-align:right;">' + 
			'<img src="'+ ooki_GetBaseURL() + 'plugins/ooki_bgsound/images/bgsound_b.gif" onmouseover="ookiBGSoundTM_Player(true);" onmouseout="ookiBGSoundTM_Player(false);">&nbsp;&nbsp;<br>' + 
			'<object id="bgsound_player" height="45" width="200" style="display:none;" onmouseover="ookiBGSoundTM_Player(true);" onmouseout="ookiBGSoundTM_Player(false);" codebase="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=5,1,52,701" classid="clsid:6BF52A52-394A-11D3-B153-00C04F79FAA6">' + 
				'<param NAME="URL" VALUE="'+ B_URL_InsertPath(u, sUrlPath) +'">' + 
				'<param NAME="playCount" VALUE="999">' + 
				'<param NAME="autoStart" VALUE="1">' + 
				'<param NAME="volume" VALUE="50">' + 
				'<embed type="application/x-mplayer2" id="webcam_audio" src="'+ u +'" width="200" height="45" autostart="true" url="'+ u +'"></embed>' + 
			'</object>' + 
		'</div>';
	
	op = B_getParentTag(n, "P");
	if ( op ) 
		op.insertAdjacentHTML('beforeBegin', h); 
	else
		n.insertAdjacentHTML('beforeBegin', h); 
}


function ookiRegisterSmartObject(o) 
{
	// CloseErrorMessage
	try {external.B_CloseErrorMessage();}catch(e){}
	
	// Video Player
	if (typeof(o) == "undefined")
		ec = document.getElementsByTagName("span");
	else {
		try{ec = o.getElementsByTagName("span");}catch(e){}
	}
	if (typeof(ec) == "undefined")
		return;
	
	var bFlash = false;
	var bFlashMP3 = false;
	var c, oA, r;
	for (var i=0; i < ec.length; i++) {
		try {
			url = null;
			c = ec[i].className;
			oA = ec[i].children[0];
			bOoki = c && c.substr(0,4) == "ooki";
			if (bOoki && !(oA && oA.nodeName == "A")) {
				// 被包了兩層 A
				$a = $(ec[i]).parents("a").next();
				if ($a.is(".ooki_player")) {
					url = $a.attr("href");
					$a.remove();
				}
				bOoki = (url ? true : false);
			}
			else {
				url = oA.href;
			}
			if (bOoki) {
				if ( ec[i].title && ec[i].title != "" )
					eval("p = {" + ec[i].title + "};");
				else
					p = {};
				p.src = url;
				r = null;
				switch (c.substr(4,c.length)) {
					case "WindowsMedia":
						// mp3 改用 flowplayer 撥放
						if (p.src.substr(p.src.length-4) == ".mp3") {
							r = '<a href="'+p.src+'" style="display:block;width:300px;height:30px;" class="ooki_flash_player_mp3"></a>';
							bFlashMP3 = true;
						}
						else {
							r = writeWindowsMedia(p,true);
						}
						break;
						
					case "Flash":
						// flv 改用 flowplayer 撥放
						//if (p.src.substr(p.src.length-4) == ".flv") {
						if (/\.flv$/i.test(p.src)) {
							r = '<a href="'+p.src+'" style="display:block;width:'+p.width+'px;height:'+p.height+'px;" class="ooki_flash_player"></a>';
							bFlash = true;
						}
						else {
							r = writeFlash(p,true);
						}
						break;
					
					case "ShockWave":
						r = writeShockWave(p,true);
						break;
						
					case "QuickTime":
						r = writeQuickTime(p,true);
						break;
						
					case "RealMedia":
						r = writeRealMedia(p,true);
						break;

						
					case "BGSound":
					case "BGSoundTM":
						ookiBGSoundTM(p, ec[i]);
						r = null;
						break;
					
					default:
						continue;
				}
				if (r) {
					ec[i].innerHTML = r;
					ec[i].className = "";
					ec[i].style.border = "";
					ec[i].style.backgroundColor = "";
				}
				else {
					ec[i].innerHTML = "";
					ec[i].removeNode();
				}
			}
		}
		catch(e) {
		}
	}
	
	if (bFlashMP3) {
		flowplayer(".ooki_flash_player_mp3", "/tools/flowplayer/flowplayer-3.2.9.swf", {
			clip: {
				autoPlay: false,
				autoBuffering: false
			},
			plugins: {
				controls: {
					autoHide: false,
					fullscreen : false,
					height: 30
				}
			}
		});
	}
	if (bFlash) {
		flowplayer(".ooki_flash_player", "/tools/flowplayer/flowplayer-3.2.7.swf", {
			plugins: {
				pseudo: { url: "/tools/flowplayer/flowplayer.pseudostreaming-3.2.7.swf" },
				controls: {
					fullscreen : true,
					height: 30
				}
			},
			clip : {
				autoPlay: false,
				autoBuffering: false,
				provider: "pseudo",
				onResume: function()  {
					if (!this.isFullscreen())
						this.toggleFullscreen();
				}
			}
		});
	}
	
	//SyntaxHighlighter.all();
	SyntaxHighlighter.highlight();
}

function ookiInit() {
	
	setTimeout(function(){
	
		//SyntaxHighlighter
		SyntaxHighlighter.config.bloggerMode = true;
		//SyntaxHighlighter.all();
		SyntaxHighlighter.highlight();
		// 
		ookiRegisterSmartObject();
		
	}, 300);
}



if(window.attachEvent)
	window.attachEvent("onload", ookiInit);
else
	window.addEventListener("load", ookiInit);



/////////////////////////
function ooki_GetBaseURL() {
	var k = "/plugins/plugin_res.js";
	var elements = document.getElementsByTagName('script');
	for (var i=0; i<elements.length; i++) {
		if (elements[i].src && (elements[i].src.indexOf(k) != -1)) {
			return elements[i].src.substr(0, elements[i].src.length -k.length +1);
		}
	}
}


function B_GetIFrame(n) {
	for (var x=0; x<frames.length; x++) {
		if (frames[x].name == n)
			return frames[x];
	}
}

function B_URL_MakePath(u, bH, bRR)
{
	u = u + "";
	if (!bH) {
		if (u.substring(0,7).toLowerCase() == "http://") {
			x = u.indexOf('/',7);
			if (x > -1) u = u.substring(x,u.length);
		}
	}
	
	x = u.indexOf("?");
	if (x > -1) u = u.substring(0,x);
	x = u.lastIndexOf("/");
	if (x > -1) u = u.substring(0,(bRR ? x+1 : x));
	
	return u;
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

function B_URL_MakePathFile(u, bRoot, bArg)
{
	u = u + "";
	if (u.substring(0,7).toLowerCase() == "http://") {
		x = u.indexOf('/',7);
		if (x > -1) {
			if (!bRoot) x++;
			u = u.substring(x,u.length);
		}
	}
	
	if (!bArg) {
		x = u.indexOf("?");
		if (x > -1) u = u.substring(0,x);
	}
	return u;
}

function B_getElementByIdTag(id, tag)
{
	var ec;
	ec = document.getElementsByName(id);
	for (var x=0; x<ec.length; x++) {
		if (ec[x].tagName == tag) {
			return ec[x];
		}
	}
}

function B_getParentTag(n, tag)
{
	while ( n && n.nodeName != tag )
		n = n.parentNode;
	return n;
}

function B_getParentTag(n,tag)
{
	while ( n && n.nodeName != tag )
		n = n.parentNode;
	return n;
}
