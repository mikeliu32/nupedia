<?php
if ( empty($_REQUEST["url"]) ) B_jError("empty url.");
if ( strtolower(substr($_REQUEST["url"],0,7)) != "http://" ) B_jError("not http://.");
$sUrl = $_REQUEST["url"];
$userAgent = !empty($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.1.4322; .NET CLR 2.0.50727; .NET CLR 3.0.04506.648; .NET CLR 3.5.21022; .NET4.0C; .NET4.0E; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729)";

$opts = array(
  'http'=>array(
    'method'=>	"GET",
    'header'=>	"Accept: image/gif, image/x-xbitmap, image/jpeg, image/pjpeg, application/x-shockwave-flash, application/vnd.ms-excel, application/vnd.ms-powerpoint, application/msword, application/x-ms-application, application/x-ms-xbap, application/vnd.ms-xpsdocument, application/xaml+xml, */*\r\n" . 
				"Accept-Language: zh-tw\r\n" . 
				"User-Agent: $userAgent\r\n"
	)
);
$context = stream_context_create($opts);
// Open the file using the HTTP headers set above
$data = @file_get_contents($sUrl, false, $context);

// B_SaveFile("page.html", $data);

$out = array();
$out['url']		= $sUrl;
Html_con_UTF8($data, $out);
$out['title']	= Html_getTitle($data);
$out['desc'] 	= Html_getDesc($data);
$out['imgs'] 	= Html_getImgs($data, $sUrl);
//$out['html'] 	= out_ToHtml($out);

$out['debug'] = B_Array2String2($out);
echo json_encode($out);

function out_ToHtml(&$out)
{
	$h = '<table width="100%" cellpadding=0 cellspacing=0 border=0><tr>';
 	if ( count($out['imgs']) > 0 ) {
		$u = $out['imgs'][0]['u'];
		$h .= 	'<td width="110px;" align="center" valign="middle">' . 
					'<img id="img" width="100px" title="'.$u.'" src="'.$u.'"><br><br>' . 
					'<a href=# rel="bnUp" class="bn2 ui-icon ui-icon-triangle-1-w"></a>' . 
					'<a href=# rel="bnDown" class="bn2 ui-icon ui-icon-triangle-1-e"></a>' . 
					' &nbsp; <span id="imginfo">1/'.count($out['imgs']).'</span>' . 
				'</td>';
	}
	$h .=	'<td>' . 
				'<div style="font-weight: bold">'.$out['title'].'</div>' . 
				'<div style="color:#808080;">'.$out['url'].'</div>' . 
				'<div >'.$out['desc'].'</div>' . 
			'</td>' . 
			'</tr></table>';
	return $h;
}

function Html_getCharset($data) 
{
	$x = 0;
	while ( preg_match("/<meta[^>]+http-equiv=\"Content-Type\"[^>]*>/i", $data, $m, PREG_OFFSET_CAPTURE, $x) ) {
		if ( preg_match("/charset=([^\"]*)\"/i", $m[0][0], $m2) ) {
			return $m2[1];
		}
		$x = $m[0][1] + strlen($m[0][0]);
	}
	return false;
}

function Html_con_UTF8(&$data, &$out) 
{
	$charset = Html_getCharset($data);
	if ( $charset === false ) {
		if ( B_is_utf8($data) ) {
			$out['charset'] = "utf-8";
			return;
		}
		else {
			$data = mb_convert_encoding($data, 'UTF-8');
			return;
		}
	}
	$out['charset'] = $charset;
	$data = mb_convert_encoding($data, 'UTF-8', $charset);
}

function Html_getTitle($data) 
{
	if ( preg_match("/<title>([^<]*)<\/title>/is", $data, $m) )
		return $m[1];
	return "";
}

function Html_getDesc($data) 
{
	if ( preg_match("/<meta name=\"description\" content=\"([^\"]*)\"/i", $data, $m) )
		return $m[1];
	return "";
}

function Html_getImgs($data, $UrlPath) 
{
	$res = array();
	$out = array();
	$x = 0;
	while ( preg_match("/<img[^>]+src=\"([^\"]*)\"/i", $data, $m, PREG_OFFSET_CAPTURE, $x) ) {
		$u = B_Url_InsertPath($m[1][0], $UrlPath);
		do { 
			if ( isset($res[$u]) ) break;
			$res[$u] = $u;
			$out[] = $u;
		} while(0);
		$x = $m[1][1] + strlen($m[1][0]);
	}
	return $out;
}

function Html_getAttr($h, $k) 
{
	if ( preg_match("/$k=\"([^\"]+)\"/i", $h, $m) )
		return $m[1];
	return false;
}

function B_jError($err)
{
	$res = array();
	$res["error"] = $err;
	print json_encode($err);
	exit;
}

function B_SaveFile($sFilePath, $sData, $nSize=0)
{
	if (!isset($sData)) return false;
	$hFile = fopen($sFilePath, "wb");
	if (!$hFile) return false;
	if ($nSize == 0) 	fwrite($hFile, $sData);
	else 				fwrite($hFile, $sData, $nSize);
	fclose($hFile);
	return true;
}

function B_is_utf8($str) {
    $c=0; $b=0;
    $bits=0;
    $len=strlen($str);
    for($i=0; $i<$len; $i++){
        $c=ord($str[$i]);
        if($c > 128){
            if(($c >= 254)) return false;
            elseif($c >= 252) $bits=6;
            elseif($c >= 248) $bits=5;
            elseif($c >= 240) $bits=4;
            elseif($c >= 224) $bits=3;
            elseif($c >= 192) $bits=2;
            else return false;
            if(($i+$bits) > $len) return false;
            while($bits > 1){
                $i++;
                $b=ord($str[$i]);
                if($b < 128 || $b > 191) return false;
                $bits--;
            }
        }
    }
    return true;
}

function B_isUrl($u) {
	return 	strtolower(substr($u,0,7)) == "http://" 
			|| strtolower(substr($u,0,8)) == "https://" ;
}

function B_Url_MakeIPPort($u, $bHttp=false, $bRootR=false)
{
	$x = 0;
	if (strtolower(substr($u,0,7)) == "http://") {
		if ($bHttp)
			$x = 7;
		else
			$u = substr($u,7);
	}
	else if (strtolower(substr($u,0,8)) == "https://") {
		if ($bHttp)
			$x = 8;
		else
			$u = substr($u,8);
	}
	
	$y = strpos($u, "/", $x);
	if ($y === false) return $u;
	if ($bRootR) $y++;
	return substr($u, 0, $y);
}

function B_Url_InsertPath($Url, $UrlPath) 
{
	if (B_isUrl($Url)) return $Url;
	if 		(substr($Url,0,2) == '//') {
		$UrlPath = "http:";
	}
	else if (substr($Url,0,1) == '/') {
		$UrlPath = B_Url_MakeIPPort($UrlPath, true, false);
	}
	else {
		if (substr($UrlPath,-1,1) != '/') $UrlPath .= '/';
	}
	return $UrlPath.$Url;
}

function B_GetExtension($sFile)
{
	$x = strrpos($sFile, '?');
	if (!($x === false))
		$sFile = substr($sFile, 0, $x);
	
	$x = strrpos($sFile, '.');
	if (strrpos($sFile, '/') > $x) $x = false;
	return $x === false ? "" : substr($sFile, $x+1);
}


function B_Array2String2($a, $bDelSpace=false, $style=0)
{
	// html
	if ($style == 0) {
		$signCol=" : ";
		$signRow="<br>";
		$signHead="";
		$signSubHead="&nbsp;&nbsp;&nbsp;&nbsp;";
	}
	// dialog
	else {
		$signCol=" : ";
		$signRow="\r\n";
		$signHead="";
		$signSubHead="\t";
	}
	return B_Array2String($a, $bDelSpace, $signCol, $signRow, $signHead, $signSubHead);
}
function B_Array2String($a, $bDelSpace=false, $signCol=" : ", $signRow="<br>", $signHead="", $signSubHead="&nbsp;&nbsp;&nbsp;&nbsp;")
{
	
	if (is_array($a) || is_object($a)) {
		$l = 0;
		foreach($a as $k => $v) 
		{
			if($bDelSpace && empty($v)) continue;
			if (is_string($v) 
				|| is_integer($v) 
				|| is_bool($v) 
				|| is_float($v)) {
				$ss .= $signHead.sprintf("%02d",++$l).". ".$k.$signCol.htmlspecialchars($v).$signRow;
			}
			else if (is_array($v) || is_object($v)) {
				$ss .= $signHead.sprintf("%02d",++$l).". ".$k.$signCol.gettype($v)."(".count($v).") : ".$signRow;
				$ss .= B_Array2String($v, $bDelSpace, $signCol, $signRow, $signHead.$signSubHead);
			}
			else {
				$ss .= $signHead.sprintf("%02d",++$l).". ".$k.$signCol."(".gettype($v).") : ".$signRow;
			}
		}
	}
	else if (is_string($a))
		$ss .= $a;
	else
		$ss .= gettype($a);
	return $ss;
}


?>