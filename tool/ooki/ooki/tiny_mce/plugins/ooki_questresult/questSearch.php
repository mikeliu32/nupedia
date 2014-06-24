<?
define("FN_ENGINE", 	"questResult.dat");
define("PV_INFO_PROG", 	"NUTools.exe");
define("UK_NUMBER", 	"<page>");

require_once("simple_html_dom.php");
require_once("questParse.php");

header("Content-Type: text/html; charset=utf-8");

set_time_limit(300);
mb_internal_encoding("UTF-8");
mb_regex_encoding("UTF-8");

$q = $_REQUEST['q'];
$I = $_REQUEST['I'];
$p = $_REQUEST['p'];
//echo "q = $q, I = $I, p = $p <br>";

$bDebug = strpos($q, "&debug") !== false;
if ($bDebug) {
	$q = substr($q,0,strlen($q)-6);
	echo "q = $q, I = $I, p = $p <br>";
}

print GetResult($q, $I, $p);


///////////////////////////////////////////////////////////////////////////////////////
function GetResult($q, $I, $p)
{
	global $bDebug;

	if (empty($p)) $p = 1;

	if ($I == "t_vdo")
	{
		$sDir_Tools = realpath("../../../../htdoc/tools/");
		chdir($sDir_Tools);
		require_once("zend_lib.php");
		
		$pc = 20;
		$arg = array();
		$arg['q']			= $q;
		$arg['p']			= ($p-1)*$pc+1;
		$arg['ps']			= $pc;
		$arg['o']			= "relevance";
		$yt_res = zend_yt_Search($arg);
//echo "yt_res:<br>".B_Array2String2($yt_res)."<br>";
		$recs = yt_con_YTRecs2Recs($yt_res['recs']);
	}
	else
	{
		$U = GetEngineURL($I, $pc);	// $pc => 每頁總數
		$U = str_replace("<keyword>", urlencode($q), $U);
		$U = URL_SetPage($U, $I, $p, $pc);
		define("SYS_URL_BASE", 	$U);
		$html = GetHtml($U);
		
		// debug
		// echo "U = $U<br>";
		// B_SaveFile("result.html", $html);
	
		$recs = parseEngine($I, $html);
	}
	
	$id = ($pc==-1 ? 1 : ($p-1)*$pc+1);
	$ss = recs2Html($I, $recs, $id);
	$ss = str_replace("\r\n", "", $ss);
	
	$PageHtml = ($pc > -1 ? GetPageHtml($I, $q, $p) : "");
	
	$out = "<table width=100%><tr><td>".$ss."</td></tr></table>".$PageHtml;
	return $out;
}

function GetEngineURL($key, &$pc)
{
	$as = file(FN_ENGINE);
	foreach($as as $s) {
		$s = trim($s);
		if (empty($s)) continue;
		list($k, $T, $U, $pc) = explode("\t", $s);
		if ($k == $key) {
			return $U;
		}
	}
}

function GetHtml($U)
{
	$pv_exe = PV_INFO_PROG;
	$f = realpath("./")."\\pv_".time();
	$cmd = "$pv_exe /DownloadURL -wait:3000 \"$U\" \"$f\"";
	system($cmd);
	$ss = B_LoadFile($f);
	unlink($f);
	return $ss;
}

function GetPageHtml($I, $q, $p)
{
	$ss = "\n<div align=center>";
	if ($p > 1) $ss .= "<a href=\"questSearch.php?I={$I}&q=".urlencode($q)."&p=".($p-1)."\" onclick=\"return checkData2(this);\" target=sr_if>Previous</a> &nbsp;";
	$ss .= "<a href=\"questSearch.php?I={$I}&q=".urlencode($q)."&p=".($p+1)."\" onclick=\"return checkData2(this);\" target=sr_if>Next</a>";
	return $ss."</div>";
}

function URL_SetPage($U, $I, $p, $pc)
{
	$pc = (int)$pc;
	if ($pc == 0) $pc = 10;
	$U =  str_replace(UK_NUMBER, ($p-1)*$pc+1, $U);
	return $U;
}

function recs2Html($I, $recs, $id)
{
	switch ($I)
	{
		case "g_web":
		case "g_news":
		case "g_blog":
		case "g_scr":
		case "y_web":
		case "y_news":
			$ss = recs2Html_web($recs, $id);
			break;
			
		case "g_img":
		case "y_img":
		case "f_img":
			$ss = recs2Html_img($recs, $id);
			break;
			
		case "y_vdo":
		case "t_vdo":
			$ss = recs2Html_vdo($recs, $id);
			break;
			
	}
	return $ss;
}

function recs2Html_web($recs, $id)
{
	if ($id < 1) $id = 1;
	$ss = "";
	$i = $id -1;
	foreach($recs as $rec)
	{
		$i++;
		$sU = $rec['U'];
		if (strlen($sU) > 50) $sU = substr($sU,0,50)."...";
		
		$m = $rec['DS'];
		if (!empty($m)) $m .= " - ";
		$m .= $sU;
		if (!empty($rec['fS'])) $m .= " - ".$rec['fS'];
		$m = empty($m) ? "" : "<span style=\"color:green\">{$m}</span><br>";
		
		if ( empty($rec['U']) )
			$hT = "<span>{$i}. {$rec['T']}</span><br>";
		else
			$hT = "<span>{$i}. <a href=\"{$rec['U']}\" style=\"color:#0000FF;\" target=_blank>{$rec['T']}</a></span><br>";
		$hC = empty($rec['C']) ? "" : "<span>{$rec['C']}</span><br>";
		//$hO = empty($rec['O']) ? "" : "<span>{$rec['O']}</span><br>";
		$hO = "";
		
$ss .= <<<_EOT_
	<table width=100% cellSpacing="0" cellPadding="2">
	  <tr>
	    <td width=1% valign=top rowspan=4><input type=checkbox id=chkItem name=chkItem style="border: 0px solid #000000;"></td>
	    <td>
	      <div id=divItem style="font-size:13px;">
	        {$hT} {$hC} {$m} {$hO}
	        <br>
	      </div>
	    </td>
	  </tr>
	</table> 
_EOT_;
	}
	return $ss;
}

function recs2Html_img($recs, $id)
{
	$ss = ""; $pi = "";
	if (substr($id,0,1) == "?") {
		$i = 0;
		$pi = substr($id, 1)."-";
	}
	else 
		$i = $id -1;
	foreach($recs as $rec)
	{
		$i++;
		$No = $pi.$i;
		$T = "";
		if (!empty($rec['T'])) $T = "title=\"".$rec['T']."\"";
		$C = "";
		if (!empty($rec['a']))
			$C .= "<b><a href=\"{$rec['aU']}\">{$rec['a']}</a></b><br>{$rec['T']}";
		$C .= $rec['C'];
		if (!empty($rec['w']) && !empty($rec['h']))
			$C .= (empty($C) ? "" : "<br>") . $rec['w']." x ".$rec['h'];
		if (!empty($rec['fS']))
			$C .= (empty($C) ? "" : " - ") . $rec['fS'];
		
$ss .= <<<_EOT_
<div style="FLOAT:left; padding:10 10 10 10;">
	<span> &nbsp;&nbsp; {$No}. <input type=checkbox id=chkItem name=chkItem style="border: 0px solid #000000;"></span>
	<table id=divItem style="float:left; width:180px; table-layout:fixed">
	  <tr><td style="text-align:center;">
		  <table cellpadding="0" cellspacing="0" border="0" style="width:160px; height:160px; text-align:center; padding:2px; border-top:1px solid #D9D9D9; border-right:3px inset #EBEBEB; border-bottom:3px inset #EBEBEB; border-left:1px solid #D9D9D9;">
			<tr> 
			  <td style="text-align:center; overflow:hidden;"><a href="{$rec['U']}" {$T} target=_blank><img src="{$rec['tn']}" border="0"></a></td>
			</tr>
		  </table>
	  </td><tr>
	  <tr><td style="overflow:hidden">
		  <div class="word-2" style="text-align:center; width:180px; height:53px; overflow:hidden; text-overflow:ellipsis; color:#666666; font-family:Arial,Helvetica,sans-serif; font-size:13px; letter-spacing:0.4pt;"> 
			{$C}
		  </div>
	  </td><tr>
	</table>
</div>
_EOT_;
	}
	return $ss;
}

function recs2Html_vdo($recs, $id)
{
	$ss = "";
	$i = $id -1;
	foreach($recs as $rec)
	{
		$i++;
		$C = mb_strimwidth($rec['C'], 0, 140, "...", "UTF-8"); 
$ss .= <<<_EOT_
	<table cellSpacing="0" cellPadding="2" style="width:100%;">
	  <tr>
	    <td width=1% valign=top rowspan=4>{$i}. <br><input type=checkbox id=chkItem name=chkItem style="border: 0px solid #000000;"></td>
	    <td>
	      <div id=divItem padding="5 0 10 0;">
	        <table cellpadding="0" cellspacing="0" border="0" style="width:100%;">
	          <tr>
	            <td style="width:140px; text-align:center;">
					<table cellpadding="0" cellspacing="0" border="0" style="width:130px; height:100px; padding:2px; border-top:1px solid #D9D9D9; border-right:3px inset #EBEBEB; border-bottom:3px inset #EBEBEB; border-left:1px solid #D9D9D9;">
					  <tr><td style="text-align:center;"><a href="{$rec['U']}" target=_blank><img src="{$rec['tn']}" border="0"></a></td></tr>
					</table>
	            </td>
	            <td style="vertical-align: top; padding:5 10 5 10; font-size:13px;">
			        <span><a href="{$rec['U']}" style="color:#0000FF;" target=_blank>{$rec['T']}</a></span><br>
			        <span>{$C}</span><br>
			        <span>{$rec['C2']}</span><br>
	            </td>
	          </tr>
	        </table>
	      </div>
	    </td>
	  </tr>
	</table> 
_EOT_;
	}
	return $ss;
}

function yt_con_YTRecs2Recs($recs)
{
	$out = array();
	foreach($recs as $rec) {
		$url = "http://www.youtube.com/watch?v=".$rec['VideoID'];
		$thunbs = "";
		if (is_array($rec['thumbs'])) {
			$w = 0;
			foreach($rec['thumbs'] as $tbs) {
				if ($tbs['width'] > $w) {
					$w = $tbs['width'];
					$thunbs = $tbs['url'];
				}
			}
		}
		list($t_date, $t_time) = explode("T", $rec['Updated']);
		$C2 = 	tpl_con_data_Duration2Html($rec['Duration'])
				."|".$t_date
				."|觀看次數：".tpl_con_data_ViewCount2Html($rec['ViewCount']);
				
		$a = array();
		$a['U']		= $url;
		$a['T']		= $rec['Video'];
		$a['tn']	= $thunbs;
		$a['C']		= $rec['Description'];
		$a['C2']	= $C2;
		$out[] 		= $a;
	}
	return $out;
}
function tpl_con_data_Duration2Html($n)
{
	$ss = $n%60;
	$n = (int)($n/60);
	if ($n < 60)
		$ss = $n.":".$ss;
	else
		$ss = (int)($n/60).":".($n%60).":".$ss;
	return $ss;
}
function tpl_con_data_ViewCount2Html($n)
{
	if (strlen($n) > 3) {
		$s = $n;
		$l = strlen($s);
		$x = $l%3;
		$cnt = (int)($l/3)+1;
		$n = ($x > 0 ? substr($s,0,$x) : "");
		while(--$cnt > 0) {
			$n .= ",".substr($s,$x,3);
			$x += 3;
		}
		if (substr($n,0,1) == ",") $n = substr($n,1);
	}
	return $n;
}

///////////////////////////////////////////////////////////////////////////////////////
// Base
// function B_Array2String($a, $bDelSpace=false, $signCol=" : ", $signRow="<br>", $signHead="", $signSubHead="　　　　")
// {
	// $l = 0;
	// foreach($a as $k => $v) 
	// {
		// if($bDelSpace && empty($v)) continue;
		// if (is_string($v) 
			// || is_integer($v) 
			// || is_bool($v) 
			// || is_float($v)) {
			// $ss .= $signHead.sprintf("%02d",++$l).". ".$k.$signCol.htmlspecialchars($v).$signRow;
		// }
		// else if (is_array($v)) {
			// $ss .= $signHead.sprintf("%02d",++$l).". ".$k.$signCol."Array(".count($v).") : ".$signRow;
			// $ss .= B_Array2String($v, $bDelSpace, $signCol, $signRow, $signHead.$signSubHead);
		// }
		// else {
			// $ss .= $signHead.sprintf("%02d",++$l).". ".$k.$signCol."(".gettype($v).") : ".$signRow;
		// }
	// }
	// return $ss;
// }

function B_LoadFile($file, $pos=0, $size=0)
{
	if (!file_exists($file)) return;
	clearstatcache();
	$hFile = fopen($file, "rb");
	if (!$hFile) return false;
	if ($pos) fseek($hFile, $pos, SEEK_SET);
	if (!$size) $size = filesize($file);
	$ss = fread($hFile, $size);
	fclose($hFile);
	return $ss;
}

function B_SaveFile($sFilePath, $Data, $Size=0)
{
	$hFile = fopen($sFilePath, "wb");
	if (!$hFile) return false;
	if ($Size == 0) 	fwrite($hFile, $Data);
	else 				fwrite($hFile, $Data, $Size);
	fclose($hFile);
	return true;
}

function B_GetCurrentTime_usec()
{
	$a = gettimeofday();
	return (double)($a['sec'].".".$a['usec']);
}
function B_Url_GetArg($u, $name)
{
	if (preg_match('#(\?|&)'.$name.'=([^&]*)(&|$)#i', $u, $m)) {
		return $m[2];
	}
	return false;
}



function B_Log($s)
{
	if ( !defined(LOG_FILE) ) B_Log_getFile( "Search", true );
	$data = date("Ymd His\t").str_replace("\r", "\\r", str_replace("\n","\\n",$s))."\n";
	
	$hFile = fopen(LOG_FILE, "ab");
	if (!$hFile) return false;
	fwrite($hFile, $data);
	fclose($hFile);
}
function B_Log_getFile($n, $bYm)
{
	if ( empty($n) ) $n = "wbase2";
	if ( empty($bYm) || $bYm !== false ) $bYm = true;
	define('LOG_FILE', $n.( $bYm ? '_'.date("Ym") : "" ).".log");
}

function B_Array2String2($a, $bDelSpace=false, $style=0, $MaxLevel=false)
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
	return B_Array2String($a, $bDelSpace, $signCol, $signRow, $signHead, $signSubHead, $MaxLevel);
}
function B_Array2String($a, $bDelSpace=false, $signCol=" : ", $signRow="<br>", $signHead="", $signSubHead="&nbsp;&nbsp;&nbsp;&nbsp;", $MaxLevel=false)
{
	if (is_array($a) || is_object($a)) 
	{
		$l = 0;
		foreach($a as $k => $v) 
		{
			if( !isset($v) ) continue;
			if ( $MaxLevel !== false && $l > $MaxLevel ) {
				$ss .= $signHead."...";
				break;
			}
			
			++$l;
			if (is_string($v) 
				|| is_integer($v) 
				|| is_float($v)) {
				$ss .= $signHead.sprintf("%02d", $l).". ".$k.$signCol.htmlspecialchars($v).$signRow;
			}
			else if (is_bool($v)) {
				$ss .= $signHead.sprintf("%02d", $l).". ".$k.$signCol.($v ? "true" : "false").$signRow;
			}
			else if (is_array($v) || is_object($v)) {
				$ss .= $signHead.sprintf("%02d", $l).". ".$k.$signCol.gettype($v)."(".count($v).") : ".$signRow;
				$ss .= B_Array2String($v, $bDelSpace, $signCol, $signRow, $signHead.$signSubHead, $signSubHead, $MaxLevel);
			}
			else {
				$ss .= $signHead.sprintf("%02d", $l).". ".$k.$signCol."(".gettype($v).") : ".$signRow;
			}
		}
	}
	else 
	{
		$v = $a;
		if (is_string($v) 
			|| is_integer($v) 
			|| is_float($v)) {
			$ss .= $signHead.gettype($v).$signCol.htmlspecialchars($v);
		}
		else if (is_bool($v)) {
			$ss .= $signHead.gettype($v).$signCol.($v ? "true" : "false");
		}
		else {
			$ss .= $signHead.gettype($v);
		}
	}
	return $ss;
}


?>
