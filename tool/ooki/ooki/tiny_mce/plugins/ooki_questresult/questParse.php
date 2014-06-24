<?
define("HiLine", "<span style=\"color:#cc0033\">");
define("HiLine_", "</span>");


function parseEngine($I, $html)
{
	//$tP = B_GetCurrentTime_usec();
	
	switch ($I)
	{
		case "g_web":
			$ss = parse_g_web($html);
			break;
			
		case "g_news":
			$ss = parse_g_news($html);
			break;
			
		case "g_img":
			$ss = parse_g_img($html);
			break;
			
		case "g_blog":
			$ss = parse_g_blog($html);
			break;
			
		case "g_scr":
			$ss = parse_g_scr($html);
			break;
			
		case "y_web":
			$ss = parse_y_web($html);
			break;
			
		case "y_news":
			$ss = parse_y_news($html);
			break;
			
		case "y_img":
			$ss = parse_y_img($html);
			break;
			
		case "y_vdo":
			$ss = parse_y_vdo($html);
			break;
			
		case "t_vdo":
			$ss = parse_t_vdo($html);
			break;
			
		case "f_img":
			$ss = parse_f_img($html);
			break;
			
	}
	
	//$tP = B_GetCurrentTime_usec() - $tP;
	//echo "time $I : $tP<br>";
	return $ss;
}

function parse_g_web($html)
{
	$obj_html = str_get_html($html);
	$es = $obj_html->find("div#ires li.g");
	$recs = array();
	foreach($es as $k => $e)
	{
		$oT = $e->find("H3 a",0);
		// URL
		$U = $oT->href;
		// Title
		$T = $oT->innertext;
		// Describe
		$C = $e->find(".st",0)->innertext;
		
		$T = preg_replace("#<br>#i","", preg_replace("#<em>#i",HiLine, preg_replace("#<\/em>#i",HiLine_, $T)));
		$C = preg_replace("#<br>#i","", preg_replace("#<em>#i",HiLine, preg_replace("#<\/em>#i",HiLine_, $C)));

		$rec = array();
		$rec['U'] = $U;
		$rec['T'] = $T;
		$rec['C'] = $C;
		$recs[] = $rec;
	}
	return $recs;
}

function parse_g_news($html)
{
	$obj_html = str_get_html($html);
	$es = $obj_html->find("div#res li.g");
	$recs = array();
	foreach($es as $k => $e)
	{
		// URL
		$U = str_replace("&amp;", "&", $e->find("a.l",0)->href);
		// Title
		$T = $e->find("a.l",0)->innertext;
		// Source
		$DS = $e->find(".slp",0)->plaintext;
		// Describe
		$C = $e->find(".st",0)->innertext;
		
		$T = preg_replace("#<EM>#i",HiLine, preg_replace("#<\/EM>#i",HiLine_, $T));
		$C = preg_replace("#<EM>#i",HiLine, preg_replace("#<\/EM>#i",HiLine_, $C));

		$rec = array();
		$rec['U'] = $U;
		$rec['T'] = $T;
		$rec['C'] = $C;
		$rec['DS'] = $DS;
		$recs[] = $rec;
	}
	return $recs;
}

function parse_g_img($html)
{
	$obj_html = str_get_html($html);
	$es = $obj_html->find("div#rg_s .rg_li");
	$recs = array();
	foreach($es as $k => $e)
	{
		$oImg = $e->find("img",0);
		$oA = $e->find("a.rg_l",0);
		$url = preg_replace("#\+#", "%20", preg_replace("#\&amp\;#i", "&", $oA->href));
		// URL
		$U = rawurldecode(B_Url_GetArg($url, "imgrefurl"));
		// Thumbs
		$tn = parse_getSection($e->innertext, "src=\"", "\"");
		//$tn = $oImg->src;
		if (empty($tn)) continue;
		// size
		$w = rawurldecode(B_Url_GetArg($url, "w"));
		$h = rawurldecode(B_Url_GetArg($url, "h"));
		
		$rec = array();
		$rec['U'] = $U;
		$rec['tn'] = $tn;
		$rec['w'] = $w;
		$rec['h'] = $h;
		$recs[] = $rec;
	}
	return $recs;
}

function parse_g_blog($html)
{
	$obj_html = str_get_html($html);
	$es = $obj_html->find("div#ires li.g");
	$recs = array();
	foreach($es as $k => $e)
	{
		$oT = $e->find("H3 a",0);
		// URL
		$U = $oT->href;
		// Title
		$T = $oT->innertext;
		// Describe
		$C = $e->find(".st",0)->innertext;
		
		$T = preg_replace("#<br>#i","", preg_replace("#<em>#i",HiLine, preg_replace("#<\/em>#i",HiLine_, $T)));
		$C = preg_replace("#<br>#i","", preg_replace("#<em>#i",HiLine, preg_replace("#<\/em>#i",HiLine_, $C)));

		$rec = array();
		$rec['U'] = $U;
		$rec['T'] = $T;
		$rec['C'] = $C;
		$recs[] = $rec;
	}
	return $recs;
}

function parse_g_scr($html)
{
	$obj_html = str_get_html($html);
	$es = $obj_html->find("div#gs_ccl .gs_r");
	$recs = array();
	foreach($es as $k => $e)
	{
		$oT = $e->find(".gs_rt a",0);
		// URL
		$U = $oT->href;
		// Title
		$T = $oT->innertext;
		if (empty($T)) continue;
		// [引言]
		$AI = $e->find(".gs_a",0)->plaintext;
		// Describe
		$C = $e->find(".gs_rs",0)->innertext;
		
		$T = preg_replace("#<font[^>]*>#i",HiLine, preg_replace("#<\/font>#i",HiLine_, $T));
		$C = preg_replace("#<font[^>]*>#i",HiLine, preg_replace("#<\/font>#i",HiLine_, $C));

		$rec = array();
		$rec['U'] = $U;
		$rec['T'] = $T;
		$rec['C'] = $C;
		$rec['AI'] = $AI;
		$recs[] = $rec;
	}
	return $recs;
}

function parse_y_web($html)
{
	$obj_html = str_get_html($html);
	$es = $obj_html->find("div#web div.res");
	$recs = array();
	foreach($es as $k => $e)
	{
		$oT = $e->find("H3 a",0);
		// URL
		$U = $oT->href;
		// Title
		$T = $oT->innertext;
		// Describe
		$C = $e->find(".abstr",0)->innertext;
		
		$T = preg_replace("#<br>#i","", preg_replace("#<b>#i",HiLine, preg_replace("#<\/b>#i",HiLine_, $T)));
		$C = preg_replace("#<br>#i","", preg_replace("#<b>#i",HiLine, preg_replace("#<\/b>#i",HiLine_, $C)));

		$rec = array();
		$rec['U'] = $U;
		$rec['T'] = $T;
		$rec['C'] = $C;
		$recs[] = $rec;
	}
	return $recs;
}

function parse_y_news($html)
{
	$obj_html = str_get_html($html);
	$es = $obj_html->find("div#web div.res");
	$recs = array();
	foreach($es as $k => $e)
	{
		$oT = $e->find("H3 a",0);
		// URL
		$U = $oT->href;
		// Title
		$T = $oT->innertext;
		// Describe
		$C = $e->find(".abstr",0)->innertext;
		// Data Souce
		$DS = $e->find(".url",0)->plaintext;
		
		$T = preg_replace("#<br>#i","", preg_replace("#<b>#i",HiLine, preg_replace("#<\/b>#i",HiLine_, $T)));
		$C = preg_replace("#<br>#i","", preg_replace("#<b>#i",HiLine, preg_replace("#<\/b>#i",HiLine_, $C)));

		$rec = array();
		$rec['U'] = $U;
		$rec['T'] = $T;
		$rec['C'] = $C;
		$rec['DS'] = $DS;
		$recs[] = $rec;
	}
	return $recs;
}

function parse_y_img($html)
{
	$obj_html = str_get_html($html);
	$es = $obj_html->find("div#results li.ld");
	$recs = array();
	foreach($es as $k => $e)
	{
		$oA = $e->find("a",0);
		$url = preg_replace("#\+#", "%20", preg_replace("#\&amp\;#i", "&", $oA->href));
		$oImg = $e->find("img",0);
		// Title
		$T = $oImg->title;
		// URL
		$U = rawurldecode(B_Url_GetArg($url, "rurl"));
		if (empty($U)) continue;
		// Thumbs
		$tn = $oImg->src;
		if (empty($tn)) continue;
		// size
		$w = $oImg->width;
		$h = $oImg->height;
		$fs = rawurldecode(B_Url_GetArg($url, "size"));
		// Describe
		$C = "";
		
		$rec = array();
		$rec['T'] = $T;
		$rec['U'] = $U;
		$rec['tn'] = $tn;
		$rec['C'] = $C;
		$rec['w'] = $w;
		$rec['h'] = $h;
		//$rec['fS'] = $fS;
		$recs[] = $rec;
	}
	return $recs;
}

function parse_y_vdo($html)
{
	$obj_html = str_get_html($html);
	$es = $obj_html->find("li.vr");
	$recs = array();
	foreach($es as $k => $e)
	{
		$oA = $e->find("a",0);
		$url = preg_replace("#\+#", "%20", preg_replace("#\&amp\;#i", "&", $oA->href));
		$oImg = $e->find("img",0);
		$oH3 = $e->find("h3",0);
		// Title
		$T = $oH3->plaintext;
		// URL
		$U = rawurldecode(B_Url_GetArg($url, "rurl"));
		if (empty($U)) continue;
		// Thumbs
		$tn = $oImg->src;
		if (empty($tn)) continue;
		// size
		$w = $oImg->width;
		$h = $oImg->height;
		$l = rawurldecode(B_Url_GetArg($url, "l")); // duration
		// Describe
		$C = "";


		
		$rec = array();
		$rec['T'] = $T;
		$rec['U'] = B_URL_Recover($U, SYS_URL_BASE);
		$rec['tn'] = $tn;
		$rec['w'] = $w;
		$rec['h'] = $h;
		$rec['l'] = $l;
		$rec['C'] = $C;
		$recs[] = $rec;
	}
	return $recs;
}

function parse_t_vdo($html)
{
	$obj_html = str_get_html($html);
	$es = $obj_html->find("#results-main-content li.result-item-video");
	$recs = array();
	foreach($es as $k => $e)
	{
		// Title
		$oT = $e->find("a.result-item-translation-title",0);
		$T = $oT->plaintext;
		// URL
		$U = $oT->href;
		if (empty($U)) continue;
		// Thumbs
		$oImg = $e->find(".video-thumb img",0);
		$tn = $oImg->getAttribute("data-thumb");
		if (empty($tn)) $tn = $oImg->src;
		if (empty($tn)) continue;
		// Describe
		$C = $e->find("p.description",0)->innertext;
		$C2 = $e->find("p.facets",0)->plaintext;

		$C = preg_replace("#<br>#i","", preg_replace("#<b>#i",HiLine, preg_replace("#<\/b>#i",HiLine_, $C)));
		
		$rec = array();
		$rec['U']	= B_URL_Recover($U, SYS_URL_BASE);
		$rec['T']	= $T;
		$rec['tn']	= $tn;
		$rec['C']	= $C;
		$rec['C2']	= $C2;
		$recs[] = $rec;
	}
	return $recs;
}

function parse_f_img($html)
{
	if (preg_match("/<DIV\s+[^>]*id=ResultsThumbsDiv[^>]*>(.*?)<DIV\s+[^>]*id=ajax_pagination[^>]*>/si", $html, $m))
		$html = $m[1];

	$recs = array();
	$xI = 0;
	parse_getItem_preg($html, "/<DIV\s+[^>]*class=ResultsThumbsChildMedium[^>]*>/i", $xI);
	while (!($xI === false))
	{
		// get item
		$sItem = parse_getItem_preg($html, "/<DIV\s+class=ResultsThumbsChildMedium[^>]*>/i", $xI);
		
		$Pos = 0;
		// URL
		if (preg_match("/<A\s+[^>]*href=\"([^\"]*)\"/i", $sItem, $m))
			$U = $m[1];
		else
			continue;
		
		// img
		if (preg_match("/<IMG\s+[^>]*src=\"([^\"]*)\"/i", $sItem, $m))
			$tn = $m[1];
		else
			continue;
		
		$T = ""; $aU = ""; $a = "";
		if (preg_match("/<P\s+[^>]*class=ResultsThumbsChildMedium[^>]*>(.*?)<\/P>/si", $sItem, $m)) {
			$sItemInfo = $m[1];
		
			// Title
			if (preg_match("/<SPAN\s+[^>]*class=PhotoTitle[^>]*>(.*?)<\/SPAN>/si", $sItemInfo, $m))
				$T = $m[1];
			
			// Author URL
			if (preg_match("/<A\s+[^>]*href=\"([^\"]*)\"/i", $sItemInfo, $m))
				$aU = $m[1];
			
			// Author
			if (preg_match("/<A\s+[^>]*>(.*?)<\/A>/si", $sItemInfo, $m))
				$a = $m[1];
		}
		
		$rec = array();
		$rec['U'] = B_URL_Recover($U, SYS_URL_BASE);
		$rec['tn'] = $tn;
		$rec['T'] = htmlspecialchars($T);
		$rec['aU'] = B_URL_Recover($aU, SYS_URL_BASE);
		$rec['a'] = htmlspecialchars($a);
		
		$recs[] = $rec;
	}
	return $recs;
}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function B_Html_Url_Check(&$Html, $URLBase)
{
	$Pos = 0;
	while ( preg_match("/<a [^>]*href=\"([^>\" ]*)\"/i", $Html, $m, PREG_OFFSET_CAPTURE, $Pos) ) {
		$u = $m[1][0];
		$Pos1 = $m[1][1];
		$L1 = strlen($u);
		$u = B_URL_Recover($u, $URLBase);
		$Html = B_Replace_Pos($Html, $u, $Pos1, $L1);
		$Pos = $Pos1 + strlen($u);
	}
}

function B_URL_Recover($sURL, $URLBase)
{
	if (!strcasecmp(substr($sURL,0,7),"http://")) return $sURL;
	if (strcasecmp(substr($URLBase,0,7),"http://")) return $sURL;	// error URL Base.
	// not ip
	if (substr($sURL,0,1) == "/") 
	{
		$x = strpos($URLBase, "/", 7);
		return $x === false ? $URLBase.$sURL : substr($URLBase,0,$x).$sURL;
	}
	// not file
	else if (substr($sURL,0,1) == "?") 
	{
		$x = strpos($URLBase, "?");
		if (!($x === false)) $URLBase = substr($URLBase,0,$x);
		return substr($URLBase,0,$x).$sURL;
	}
	// not path
	else
	{
		$x = strpos($URLBase, "?");
		if (!($x === false)) $URLBase = substr($URLBase,0,$x);
		$x = strrpos($URLBase, "/");
		if (!($x === false)) $URLBase = substr($URLBase,0,$x+1);
		return substr($URLBase,0,$x).$sURL;
	}
}

function B_Replace_Pos($String, $NewString, $Pos, $L=0)
{
	return substr($String, 0, $Pos) . $NewString . substr($String, $Pos+$L);
}

function parse_getSection(&$Str, $s1, $s2, &$Pos=null, $bGetLast=false)
{
	$x = (isset($Pos) ? $Pos : 0);
	$ss = "";
	$x = strpos($Str, $s1, $x);
	if ($x === false) return false;
	$x += strlen($s1);
	$y = strpos($Str, $s2, $x);
	if ($y === false) {
		if ($bGetLast) {
			$y = strlen($Str);
			$s2 = "";
		}
		else
			return false;
	}
	$v = substr($Str, $x, $y-$x);
	$Pos = $y + strlen($s2);
	return $v;
}

function parse_getSection_preg(&$Str, $Pat1, $Pat2, &$Pos=0, $bGetLast=false)
{
	parse_getItem_preg($Str, $Pat1, $Pos);
	if ($Pos === false) return false;
	$out = parse_getItem_preg($Str, $Pat2, $Pos);
	if ($Pos === false && $bGetLast === false)
		$out = false;
	return $out;
}

function parse_getItem(&$Str, $s1, &$Pos)
{
	$x = strpos($Str, $s1, $Pos);
	if ($x === false) 
	{
		$ss = substr($Str, $Pos);
		$Pos = false;
	}
	else 
	{
		$ss = substr($Str, $Pos, $x-$Pos);
		$Pos = $x + strlen($s1);
	}
	return $ss;
}

function parse_getItem_preg($Str, $Pat, &$Pos=0)
{
	global $parse_getItem_preg_i;
	if ( !isset($parse_getItem_preg_i) ) $parse_getItem_preg_i = 0;
	
	$ss = "";
	if ( preg_match($Pat, $Str, $m, PREG_OFFSET_CAPTURE, $Pos) )
	{
		$C = $m[0][0]; 
		$p = $m[0][1];
//		echo "parse_getItem_preg No:".(++$parse_getItem_preg_i).", Pos=$Pos, p=$p, l=".strlen($C).", ll=".strlen($Str).", C=".htmlspecialchars($C).", <br>";
		$ss = substr($Str, $Pos, $p - $Pos);
		$Pos = $p + strlen($C);
	}
	else
	{
//		echo "parse_getItem_preg No:".(++$parse_getItem_preg_i).", Pos=$Pos, p=-1, l=0, ll=".strlen($Str)."<br>";
		$ss = substr($Str, $Pos);
		$Pos = false;
	}
	return $ss;
}

?>