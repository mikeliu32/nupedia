<?php
define( "FILETYPE_EXT_IMG", ",jpg,gif,png,bmp,ico,jpeg,pcd,giif,tiff," );
define( "FILETYPE_EXT_TXT", ",txt,log,ini,php,css,js,htm,html,cpp,h,c,rc,vb," );
define( "FILETYPE_EXT_DOC", ",doc,docx,dot,rtf," );
define( "FILETYPE_EXT_XLS", ",xls,xlsx,xlt," );
define( "FILETYPE_EXT_PPT", ",ppt,pptx,pps,pot," );
define( "FILETYPE_EXT_PDF", ",pdf," );
define( "FILETYPE_EXT_VIDEO", ",avi,mpg,wmv,mpeg,mov,mp4,mts,m2ts,m2t,rm,rmvb" );  
define( "FILETYPE_EXT_AUDIO", ",mp3,wma,wav,ape,flac,acc," );
define( "FILETYPE_EXT_FLASH", ",flv,mov,swf," );
define( "FILETYPE_EXT_ZIP", ",zip,rar,tar,cab,7z,gz,tgz,arj,lzh,ace," );

define( "EXT_THUMBS", ".thumbs.jpg" );
define( "EXT_THUMBS_FLV", ".src.thumbs.jpg" );
define( "PATH_WebRoot", "/data/HTTPD/htdocs" );

define( "NUWEB_REC_PATH", ".nuweb_rec/");


// 將網頁的影片撥放改用 Flv 撥放
function ooki_link_video2flv($Html)
{
	$filelist = array();
	$Html = ooki_image_2thumbs($Html, $filelist);
	$Html = ooki_video_play2flv($Html, $filelist);
	$Html .= ooki_filelist_2attach($filelist);
	return $Html;
}


function ooki_filelist_2attach($filelist)
{
	if (count($filelist) == 0)
		return "";
	$tpl = 
		'<table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-bottom:5px; border:#CCC 1px dotted;">'
			.'<tr>'
				.'<td width="100px" align="center" valign="middle" style="padding:10px">'
					.'<a href="##TEMP_Url_View##" target="_blank"><img id="NUImage2" width="100px" src="##TEMP_Url_Thumbs##"></a>'
				.'</td>'
				.'<td valign="middle" style="padding:5px 0;">'
					.'<table width="100%" border="0" cellpadding="0" cellspacing="0">'
						.'<tr> '
							.'<td width="50px" style="padding:3px 0; white-space:nowrap;">檔名：</td>'
							.'<td >##TEMP_FileName##</td>'
						.'</tr>'
						.'<tr> '
						.'<tr> '
							.'<td style="padding:3px 0; white-space:nowrap;">大小：</td>'
							.'<td >##TEMP_FileSize##</td>'
						.'</tr>'
							.'<td colspan="2" style="padding:1px; white-space:nowrap; text-decoration:inherit">'
								.'<a href="##TEMP_Url_View##" target="_blank" style="text-decoration:underline;"> 檢視 </a> &nbsp;'
								.'<a href="##TEMP_Url_Down##" style="text-decoration:underline;"> 下載 </a>'
							.'</td>'
						.'</tr>'
					.'</table>'
				.'</td>'
			.'</tr>'
		.'</table>';
	$out = '<div style="padding:10px; clear:both;">'
				.'<button onclick="$(\'#ooki_se_attach\').show(); $(this).remove();">'
					.'顯示 附件檔<span class="ui-button-icon-secondary ui-icon ui-icon-triangle-1-s"></span>'
				.'</button>'
			.'</div>'
			.'<div id="ooki_se_attach" style="display:none;">';
	foreach($filelist as $rec) {
		$u_fp = $rec['fp'];
		$read_file = PATH_WebRoot.$u_fp;
		$info = ooki_file_get_rec($read_file);
		if ($info !== false) {
			$fn = $info['filename'];
			$size = ooki_con_Size2Abbr($info['size'])."B";
		} else {
			$fn = ooki_b_GetFileNameExtension($u_fp);
			$size = ooki_con_Size2Abbr(filesize($read_file))."B";
		}
		$out .= str_replace("##TEMP_Url_Thumbs##", "/tools/api_get_thumbs.php?type=Image&page_url=".$u_fp,
				str_replace("##TEMP_FileName##", $fn,
				str_replace("##TEMP_FileSize##", $size,
				str_replace("##TEMP_Url_View##", $u_fp,
				str_replace("##TEMP_Url_Down##", "/tools/page/show_page.php?mode=download&page_url=".$u_fp,
				$tpl)))));
	}
	return $out.'</div>';
}



function ooki_file_get_rec($read_file)
{
	$dir = ooki_b_Url_MakePath($read_file, false, true);
	$FileName = ooki_b_GetFileNameExtension($read_file);
	$f_rec = $dir.NUWEB_REC_PATH.$FileName.".rec";
	if (!file_exists($f_rec))
		return false;
	
	$rec = ooki_rec_File2Rec($f_rec);
	return $rec;
}

// 將網頁的圖片改用縮圖顥示
function ooki_image_2thumbs($Html, &$out_filelist)
{
	$re1 = "#<img [^>]*src=\"([^\">]*)\"[^>]*>#is";
	$re2 = "#src=\"([^\"> ]*)\"#i";
	$re3 = "#width:\s*(\d+)px#i";
	$x = 0;
	while ( preg_match($re1, $Html, $m1, PREG_OFFSET_CAPTURE, $x) ) 
	{
		$sh = $m1[0][0];
		$sp = $m1[0][1];
		$sl = strlen($sh);
		
		$u_fp = $m1[1][0];
		if (substr($u_fp, 0, 6) == "/Site/") {
		
			if (preg_match($re2, $sh, $m2, PREG_OFFSET_CAPTURE)) {
				$u_fp = $m2[1][0];
				$u_p = $m2[1][1];
				$u_l = strlen($u_fp);
			
				// 取得寬度
				$w = 0;
				if (preg_match($re3, $sh, $m3))
					$w = (int)$m3[1];
				$u = "/tools/api_get_thumbs.php?type=Image&page_url=".$u_fp;
				if ($w == 0)
					$u .= "&size=640";
				else if ($w > 640)
					$u .= "&size=1920";
				else if ($w > 300)
					$u .= "&size=640";
				$h_img = ooki_b_replace_pos($sh, $u, $u_p, $u_l);
				$sh = "<a target=\"_blank\" href=\"".$u_fp."\">".$h_img."</a>";
				$Html = ooki_b_replace_pos($Html, $sh, $sp, $sl)."<br><br>";
				//
				$out_filelist[] = array("fp" => $u_fp, "type" => "Image");
			}			
		}
		$x = $sp + strlen($sh);
	}
	return $Html;
}


// 將網頁的影片撥放改用 Flv 撥放
function ooki_video_play2flv($Html, &$out_filelist)
{
	$re1 = "#<span [^>]*class=\"ookiWindowsMedia\"[^>]*>.*?</span>#is";
	$re2 = "#href=\"([^\"> ]*)\"#i";
	$re3 = "#title=\"([^\"> ]*)\"#i";
	$x = 0;
	while ( preg_match($re1, $Html, $m1, PREG_OFFSET_CAPTURE, $x) ) 
	{
		$sh = $m1[0][0];
		$sp = $m1[0][1];
		$sl = strlen($sh);
		
		$b = false;
		// 取 url 
		if ( preg_match($re2, $sh, $m2, PREG_OFFSET_CAPTURE) ) 
		{
			$u_fp = $m2[1][0];
			$info = ooki_URL2Info_video($u_fp);
			if ( $info !== false ) 
			{
 				// 取參數
				if ( preg_match($re3, $sh, $m3, PREG_OFFSET_CAPTURE) ) {
					$info['arg'] = $m3[1][0];
				}
				
				// if (strtolower(substr($u_fp,-4)) == ".wmv") {
					// $sh = ooki_link_video2wmv_info2Html($info);
					// $Html = ooki_b_replace_pos($Html, $sh, $sp, $sl)."<br><br>";
				// }
				//else if (!empty($info['flv'])) {
				if (!empty($info['flv'])) {
					$sh = ooki_link_video2flv_info2Html($info);
					$Html = ooki_b_replace_pos($Html, $sh, $sp, $sl)."<br><br>";
				}
				//
				$out_filelist[] = array("fp" => $u_fp, "type" => "Video");
			}
		}
		$x = $sp + strlen($sh);
	}
	return $Html;
}

function ooki_URL2Info_video($url)
{
	if ( substr($url,0,1) != "/" ) return false;
	$url = htmlspecialchars_decode($url);
	$Type = ooki_con_FileName2Type($url);
	if ( $Type != "video" && $Type != "flash" ) return false;
	$dir = ooki_b_Url_MakePath($url, true, false);
	$fn = ooki_b_GetFileNameExtension($url);
	$img_file = $url.EXT_THUMBS_FLV;
	$flv_file = $dir."/.nuweb_media/".$fn.".flv";
	if ( !file_exists(PATH_WebRoot."/".$flv_file) ) {
		$flv_file = "";
	}
	
	$res = array();
	$res['url'] = $url;
	$res['img'] = $img_file;
	$res['flv'] = $flv_file;
	return $res;
}


function ooki_link_video2flv_wall($Html)
{
	$re1 = "#<span [^>]*class=\"ookiWindowsMedia\"[^>]*>.*?</span>#is";
	$re2 = "#href=\"([^\"> ]*)\"#i";
	$re3 = "#title=\"([^\"> ]*)\"#i";
	$x = 0;
	while ( preg_match($re1, $Html, $m1, PREG_OFFSET_CAPTURE, $x) ) 
	{
		$sh = $m1[0][0];
		$sp = $m1[0][1];
		$sl = strlen($sh);
		
		$b = false;
		// 取 url 
		if ( preg_match($re2, $sh, $m2, PREG_OFFSET_CAPTURE) ) 
		{
			$info = ooki_URL2Info_video_wall($m2[1][0]);
			if ( $info !== false ) 
			{
 				// 取參數
				if ( preg_match($re3, $sh, $m3, PREG_OFFSET_CAPTURE) ) {
					$info['arg'] = $m3[1][0];
				}
				
				$sh = ooki_link_video2flv_info2Html($info);
				$Html = ooki_b_replace_pos($Html, $sh, $sp, $sl);
			}
			
		}
		
		$x = $sp + strlen($sh);
	}
	
	return $Html."<br><br>";
}

function ooki_URL2Info_video_wall($url)
{
	$url = htmlspecialchars_decode($url);
	$fn = ooki_b_Url_GetArg($url, "fn");
	if ($fn === false) return false;
	$Type = ooki_con_FileName2Type($fn);
	if ($Type != "video" && $Type != "flash") return false;
	$flv_file = "db/media/.nuweb_media/".$fn.".flv";
	$img_file = "db/media/".$fn.EXT_THUMBS_FLV;
	if ( !file_exists($flv_file) ) return false;
	
	$res = array();
	$res['flv'] = $flv_file;
	$res['img'] = $img_file;
	$res['url'] = ooki_b_Url_SetArg($url, "fn", $flv_file);
	return $res;
}

function ooki_link_video2wmv_info2Html($info)
{
 	$u_flv = $info['flv'];
	$info_arg = "{".str_replace("'", "\"", $info['arg'])."}";
	$aArg = json_decode($info_arg, true);
	$w = !empty($aArg['width']) ? $aArg['width'] : "540";
	$h = !empty($aArg['height']) ? $aArg['height'] : "405";
	$h_ico = $h - 58;
	$arg = "'width':'{$w}','height':'{$h}','autostart':true,'stretchtofit':true";
	
return <<<_EOT_
<script>
function ookiImg2WMV_OnClick(o){
	var o=$(o), u;
	u = o.attr("d_u");
	o.before(	'<div><span class="ookiWindowsMedia" title="' + o.attr("title") + '">'
				+	'<a href="' + u + '">ooki Media player</a>'
				+'</span></div>' );
	ookiRegisterSmartObject( o.prev().get(0) );
	o.remove();
}
</script>
<div class="ookiImg2WMV" style="position:relative; cursor:pointer;" onclick="ookiImg2WMV_OnClick(this);" title="{$arg}" d_u="{$info['url']}">
	<img src="{$info['img']}" width="{$w}" height="{$h}">
	<img src="/tools/ooki/media.png" style="position:absolute; left:10px; top:{$h_ico}px;">
</div>

_EOT_;

}

function ooki_link_video2flv_info2Html($info)
{
 	$u_flv = $info['flv'];
	$info_arg = "{".str_replace("'", "\"", $info['arg'])."}";
	$aArg = json_decode($info_arg, true);
	$w = !empty($aArg['width']) ? $aArg['width'] : "540";
	$h = !empty($aArg['height']) ? $aArg['height'] : "405";
	$arg = "width:'{$w}',height:'{$h}',startimage:'".$info['img']."'";
return <<<_EOT_
<span class="ookiFlash" title="{$arg}" d_u="{$info['url']}">
	<a href="{$u_flv}">ooki Media player</a>
</span>
_EOT_;

}




///////////////////////////////////////////////////////////////////////////
function ooki_b_MakePath($u, $bRootR=false)
{
	$x = strrpos($u, "?");
	if ($x === false) $x = 0;
	$x = strrpos($u, "/", $x);
	if ($x === false) return "";
	if ($bRootR) $x++;
	return substr($u, 0, $x);
}
function ooki_b_Url_GetArg($u, $n)
{
	if ( ooki_b_Url_GetArg_Pos($u, $n, $Pos, $L) === false )
		return false;
	return substr($u, $Pos, $L);
}
function ooki_b_Url_GetArg_Pos($u, $n, &$Pos, &$L)
{
	$Pos = strrpos($u, "?");
	if ($Pos === false) $Pos = 0;
	else $Pos++;
	$k = $n."=";
	$lk = strlen($k);
	if ( substr($u, $lk) == $k ) {
		$Pos += $lk;
	}
	else {
		$k = "&".$k;
		$lk = strlen($k);
		$Pos = strpos($u, $k, $Pos);
		if ( $Pos === false )
			return false;
		$Pos += $lk;
	}
	
	$y = strpos($u, "&", $Pos);
	if ( $y === false ) 
		$y = strlen($u);
	
	$L = $y - $Pos;
	return $Pos;
}
function ooki_b_Url_SetArg($u, $n, $v)
{
	$x = strrpos($u, "?");
	if ($x === false) $x = 0;
	else $x++;
	if ( preg_match("#(^|&)".$n."=([^&]*)(&|$)#i", $u, $m, PREG_OFFSET_CAPTURE, $x) )
		return substr($u, 0, $m[2][1]) . $v . substr($u, $m[2][1]+strlen($m[2][0]));
		
	$u .= ($x == 0 ? "?" : "&");
	$u .= $n."=".$v;
	return $u;
}

function ooki_b_Url_DelIP($u, $bR=true)
{
	if (strtolower(substr($u,0,7)) == "http://") {
		$x = strpos($u, "/", 7);
		if ($x === false) return "";
		if (!$bR) $x++;
		$u = substr($u, $x);
	}
	else if (!$bR)
		if (substr($u,0,1) == '/') $u = substr($u,1);
	return $u;
}

function ooki_b_Url_MakePath($u, $bHttp=true, $bRootR=false)
{
	$x = 0;
	if ($bHttp) $u = ooki_b_Url_DelIP($u);
	
	$x = strrpos($u, "/");
	if ($x === false) return "";
	if ($bRootR) $x++;
	return substr($u, 0, $x);
}

function ooki_b_GetFileNameExtension($sFile)
{
	$x = strrpos($sFile, '?');
	if (($x !== false))
		$sFile = substr($sFile, 0, $x);
	
	$x = strrpos($sFile, '\\');
	if ($x === false)
		$x = strrpos($sFile, '/');
	return $x === false ? $sFile : substr($sFile, $x+1);
}

function ooki_b_replace_pos($String, $NewString, $Pos, $L=0)
{
	return substr($String, 0, $Pos) . $NewString . substr($String, $Pos+$L);
}

function ooki_b_GetExtension($sFile)
{
	$x = strrpos($sFile, '?');
	if (!($x === false))
		$sFile = substr($sFile, 0, $x);
	
	$x = strrpos($sFile, '.');
	if (strrpos($sFile, '/') > $x) $x = false;
	return $x === false ? "" : substr($sFile, $x+1);
}
function ooki_b_LoadFile($file, $pos=0, $size=0)
{
	if (!file_exists($file)) return;
	clearstatcache();
	$hFile = fopen($file, "rb");
	if (!$hFile) return false;
	if ($pos) fseek($hFile, $pos, SEEK_SET);
	if (!$size) $size = filesize($file);
	if ($size > 0) $ss = fread($hFile, $size);
	fclose($hFile);
	return $ss;
}
function ooki_rec_File2Rec($File, $key=null)
{
	return ooki_rec_Data2Rec(ooki_b_LoadFile($File), $key);
}
function ooki_rec_Data2Rec($Data)
{
	if (substr($Data, -1, 1) == "\n") 
		$Data = substr($Data, 0, strlen($Data)-1);
	
	$ss = array();
	$aData = explode("\n", $Data);
	foreach($aData as $Data) 
	{
		if (substr($Data, -1, 1) == "\r")
			$Data = substr($Data, 0, strlen($Data)-1);
			
		if (substr($Data,0,1) == "@") 
		{
			$a = explode(":", $Data, 2);
			if (count($a) >= 2) {
				$k = substr($a[0], 1);
				$ss[$k] = $a[1];
			}
		}
		else
		{
			if (empty($k)) continue;
			$ss[$k] .= "\n".$Data;
		}
	}
	return $ss;
}
function ooki_rec_Data2Recs($Data, $key=null, $bSort=true)
{
	if (empty($Data)) return array();
	if (substr($Data, -1, 1) == "\n") 
		$Data = substr($Data, 0, -1);
	
	$aData = explode("\n", $Data);
	$bKey = !empty($key);
	$ss = array();
	foreach($aData as $Data)
	{
		if (substr($Data, -1) == "\r")
			$Data = substr($Data, 0, -1);
		
		if (substr($Data, 0, 1) == "@") 
		{
			$a = explode(":", $Data, 2);
			if ($a[0] == "@") {
				if (!empty($s)) {
					if ($bKey)
						$ss[$s[$key]] = $s;
					else
						$ss[] = $s;
				}
				$s = array();
			}
			// key 太大當上一筆資料
			else if (count($a) == 1 || strlen($a[0]) > 128) {
				$s[$k] .= "\n".$Data;
			}
			else {
				$k = substr($a[0], 1);
				$s[$k] = $a[1];
			}
		}
		else if (!empty($k))
			$s[$k] .= "\n".$Data;
	}
	if (!empty($s)) {
		if ($bKey)
			$ss[$s[$key]] = $s;
		else
			$ss[] = $s;
	}
	if ($bKey && $bSort) ksort($ss);
	return $ss;
}

function ooki_con_Size2Abbr($n)
{
	$u = "";
	if( $n > 1000000000 ) {
		$n = $n / 1000000000;
		$u = "G";
	}
	else if( $n > 1000000 ) {
		$n = $n / 1000000;
		$u = "M";
	}
	else if( $n > 1000 ) {
		$n = $n / 1000;
		$u = "K";
	}
	// 取前三碼, 四捨五入
	if ( $n < 10 )
		$n = substr( ($n*10)/10, 0, 3 );
	else if ( $n < 100 )
		$n = substr( ($n*10)/10, 0, 4 );
	else 
		$n = substr( $n, 0, 3 );
			
	return $n.$u;
}
function ooki_con_FileName2Type($FileName)
{
	$Type = "none";
	$ext = strtolower(ooki_b_GetExtension($FileName));
 	if (!empty($ext)) {
		if 		( strpos(FILETYPE_EXT_IMG, $ext) !== false )
			$Type = "image";
		else if ( strpos(FILETYPE_EXT_TXT, $ext) !== false )
			$Type = "txt";
		else if ( strpos(FILETYPE_EXT_DOC, $ext) !== false )
			$Type = "doc";
		else if ( strpos(FILETYPE_EXT_XLS, $ext) !== false )
			$Type = "xls";
		else if ( strpos(FILETYPE_EXT_PPT, $ext) !== false )
			$Type = "ppt";
		else if ( strpos(FILETYPE_EXT_PDF, $ext) !== false )
			$Type = "pdf";
		else if ( strpos(FILETYPE_EXT_VIDEO, $ext) !== false )
			$Type = "video";
		else if ( strpos(FILETYPE_EXT_AUDIO, $ext) !== false )
			$Type = "audio";
		else if ( strpos(FILETYPE_EXT_FLASH, $ext) !== false )
			$Type = "flash";
		else if ( strpos(FILETYPE_EXT_ZIP, $ext) !== false )
			$Type = "zip";
	}
 	return $Type;
}


?>