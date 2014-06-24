<?
define("FN_ENGINE", "questResult.dat");

$engHtml = GetEngineHtml();

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title> quest Result </title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="../../themes/advanced/skins/default/dialog.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript" src="../plugin_nupopup.js"></script>
<script language="javascript" type="text/javascript" src="questResult.js"></script>
<style type="text/css">
<!--
.style1 {font-family: Arial, Helvetica, sans-serif; font-size: 12px;}
.panel_wrapper2 {
	border: 1px solid #919B9C;
	padding: 10px;
	padding-top: 5px;
	clear: both;
	background-color: white;
}
-->
</style>
</head>
<body style="background-color:#fefefe;">
<table id=tt width="100%" height="100%" cellpadding=0 cellspacing=0>
  <tr><td valign=top style="padding-top:5px;">
	<div style="height:100%;">
	  <table width=100% height=100% cellpadding=0 cellspacing=0 border=0>
		<tr>
		  <td height=1%>
			<form id=form1 method=post action="questSearch.php" onsubmit="return checkData();" target="sr_if">
			  <input id=I name=I type="hidden" value="">
			  <fieldset>
				<table width=100% cellpadding="2px" cellspacing=0 border=0>
				  <tr>
					<td width=2% height=20 style="font-size:12px;" nowarp>Quest:</td>
					<td style="font-size:12px;"><input type="text" id=q name=q value="<? echo $k; ?>" size="20" class="required number min1"> &nbsp; <input type=submit value="{$lang_questResult_Search}"></td>
				  </tr>
				  <tr>
					<td width=2% height=25 style="font-size:12px;">Engine:</td>
					<td class=style1 style="font-size:12px;"><? echo $engHtml; ?></td>
				  </tr>
				</table>
			  </fieldset>
			</form>
		  </td>
		</tr>
		<tr>
		  <td valign=top style="border:solid 1px #999999;">
			<div id=divResult style="width:100%;height:100%;overflow:scroll;"></div>
		  </td>
		</tr>
	  </table>
	</div>

  </td></tr>
  <tr><td height=30>
	<div class="mceActionPanel">
		<input type="button" id="insert" name="insert" value="{$lang_insert}" onclick="BnInsert();" />
		<input type="button" id="cancel" name="cancel" value="{$lang_cancel}" onclick="BnCancel();" />
	</div>
  </td></tr>
</table>
<iframe id="sr_if" name="sr_if" style="display:none;" onload="fs_OK();"></iframe>

</body>
</html>

<?
///////////////////////////////////////////////////////////////////////////////////////
function GetEngineHtml()
{
	$as = file(FN_ENGINE);
	$id = 0;
	foreach($as as $s) {
		$s = trim($s);
		if (empty($s)) continue;
		list($k, $T, $U) = explode("\t", $s);
		$sChecked = (++$id == 1 ? "checked" : "");
		$ss .= "<span style=\"width:120px;\"><label for=\"rdoEng{$id}\"><input id=\"rdoEng{$id}\" name=rdoEng type=\"radio\" class=input_noborder onclick=\"rdo_Change()\" value=\"{$k}\" {$sChecked}>{$T}</label></span>";
	}
	return $ss;
}

function GetEngineHtml2()
{
	$as = file(FN_ENGINE);
	$ss = "<select size=1 id=I name=I>";
	$id = 0;
	foreach($as as $s) {
		$s = trim($s);
		if (empty($s)) continue;
		list($k, $T, $U) = explode("\t", $s);
		if (++$id == 1)
			$ss .= "<option value=\"{$k}\" selected>{$T}</option>";
		else
			$ss .= "<option value=\"{$k}\">{$T}</option>";
	}
	$ss .= "</select>";
	return $ss;
}
?>