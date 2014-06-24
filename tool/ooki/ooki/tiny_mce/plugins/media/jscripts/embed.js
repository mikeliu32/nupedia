/**
 * This script contains embed functions for common plugins. This scripts are complety free to use for any purpose.
 */

function writeFlash(p,br) {
	if (/\.flv/i.test(p.src)) {
		return writeEmbedFlv(
			'D27CDB6E-AE6D-11cf-96B8-444553540000',
			'http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0',
			'application/x-shockwave-flash',
			p,
			br
		);
	}
	else {
		return writeEmbed(
			'D27CDB6E-AE6D-11cf-96B8-444553540000',
			'http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0',
			'application/x-shockwave-flash',
			p,
			br
		);
	}
}

function writeShockWave(p,br) {
	return writeEmbed(
	'166B1BCA-3F9C-11CF-8075-444553540000',
	'http://download.macromedia.com/pub/shockwave/cabs/director/sw.cab#version=8,5,1,0',
	'application/x-director',
		p,
		br
	);
}

function writeQuickTime(p) {
	return writeEmbed(
		'02BF25D5-8C17-4B23-BC80-D3488ABDDC6B',
		'http://www.apple.com/qtactivex/qtplugin.cab#version=6,0,2,0',
		'video/quicktime',
		p,
		br
	);
}

function writeRealMedia(p,br) {
	return writeEmbed(
		'CFCDAA03-8BE4-11cf-B84B-0020AFBBCCFA',
		'http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0',
		'audio/x-pn-realaudio-plugin',
		p,
		br
	);
}

function writeWindowsMedia(p,br) {
	p.url = p.src;
	return writeEmbed(
		'6BF52A52-394A-11D3-B153-00C04F79FAA6',
		'http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=5,1,52,701',
		'application/x-mplayer2',
		p,
		br
	);
}

function writeEmbed(cls, cb, mt, p, br) {
	var h = '', n;
	p.src = B_URL_InsertPath(p.src, media_MakePath(document.location, true, true));
	if (p.url) p.url = B_URL_InsertPath(p.url, media_MakePath(document.location, true, true));

	h 	= '<object classid="clsid:' + cls + '" codebase="' + cb + '"'
		+ (typeof(p.id) != "undefined" ? ' id="' + p.id + '"' : '')
		+ (typeof(p.name) != "undefined" ? ' name="' + p.name + '"' : '')
		+ (typeof(p.width) != "undefined" ? ' width="' + p.width + '"' : '')
		+ (typeof(p.height) != "undefined" ? ' height="' + p.height + '"' : '')
		+ (typeof(p.align) != "undefined" ? ' align="' + p.align + '"' : '')
		+ '>';

	for (n in p)
		h += '<param name="' + n + '" value="' + p[n] + '">';

	h += '<embed type="' + mt + '"';

	for (n in p)
		h += n + '="' + p[n] + '" ';

	h += '></embed></object>';
	if (br)
		return h;
	else
		document.write(h);
}

function writeEmbedFlv(cls, cb, mt, p, br) 
{
	var h, n, ho, he;
	var sUrlPath = media_MakePath(document.location, false, true);
	//var src = B_URL_MakePathFile(B_URL_InsertPath(p.src,sUrlPath), true, true);
	var src = writeEmbedFlv_src_encode( B_URL_InsertPath(p.src,sUrlPath) );
	var FlvUrlPath = B_GetBaseURL("embed.js") + "player_flv_maxi.swf";
	if ( typeof(ooki_flv_q1) != "undefine" ) FlvUrlPath += "?cache=" + new Date().getTime();
	if ( src.indexOf("%") == -1 )  src = encodeURIComponent(src);
	
	ho=""; he="";
	for (n in p) {
		if ( n == "src" || n == "width" || n == "height" ) continue;
		ho += '<param name="' + n + '" value="' + p[n] + '">';
		he += '&' + n + "=" + p[n];
	}

	h 	= 	'<object id="monFlash" type="' + mt + '" data="' + FlvUrlPath + '"'
		+ 		(typeof(p.width) != "undefined" ? ' width="' + p.width + 'px"' : '')
		+ 		(typeof(p.height) != "undefined" ? ' height="' + p.height + 'px"' : '')
		+	'>' 
	
		+ 	'<param name="movie" value="' + FlvUrlPath + '" />' 
		+	'<param name="allowFullScreen" value="true" />' 
		+	ho
		+	'<param name="FlashVars" value="flv=' + src 
		+ 		(typeof(p.width) != "undefined" ? '&width=' + p.width : '') 
		+ 		(typeof(p.height) != "undefined" ? '&height=' + p.height : '') 
		+		'&srt=1&showfullscreen=1&showvolume=1&showtime=1' + he + '" />' 

		+	'</object>';
	if (br)
		return h;
	else
		document.write(h);
}

function writeEmbedFlv_src_encode(src)
{
	var x = src.indexOf("?"), ufp;
	if ( x == -1 ) {
		return src.indexOf("%") == -1 ? encodeURIComponent(src) : src;
	}
	else {
		return encodeURIComponent(src);
	}
}

function media_MakePath(u, bH, bRR)
{
	u = u + "";
	if (u.indexOf("pv_article.php?") > -1)
		u = u.replace(/[^\?]*\?UP=([^&]*)/, "$1");

	if (!bH) {
		if (B_IsUrl(u)) {
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

function B_IsUrl(u) 
{
	return u.substring(0,7).toLowerCase() == "http://" || u.substring(0,8).toLowerCase() == "https://";
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

