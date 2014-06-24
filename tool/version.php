<?php
date_default_timezone_set('Asia/Taipei');

include_once('inc/auth.php');
include_once('pathManage.php');

$IS_ENTRY_EXIST = (strlen($sitePath)>1 && strlen($entryID)>1 && is_dir($dataHome.$sitePath));

if($IS_ENTRY_EXIST){
	$metafile = $dataHome.$sitePath."/metainfo.json";
	$metainfo = json_decode(file_get_contents($metafile));

	$meta = $metainfo->meta;

	$historyFile = $dataHome.$sitePath."/history.json";
	$history = json_decode(file_get_contents($historyFile));

	if($IS_LOGIN)
		$IS_AUTHOR = isAuthor($metainfo->author);
}

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="css/normalize.css" type="text/css">
<link rel="stylesheet" href="css/nupedia_all.css" type="text/css">
<link rel="stylesheet" href="css/versionPage.css" type="text/css">
<link rel="stylesheet" href="css/fonts.css" type="text/css">

<link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" type="text/css">
<!--script src="http://code.jquery.com/jquery-1.10.2.min.js"></script-->
<script src="js/jquery-1.11.0.min.js"></script>

<title>NuPedia</title>
</head>

<body>
<?php
require_once('header.php');
?>

<main class="main-wrapper">
<?php
	if(!$IS_ENTRY_EXIST):
?>
	<div class="errormsg">資料不存在</div>
<?php
	else:
?>
<div class="main-leftaside">
<span class="backToEntryBtn"><a href="index.php?site=<?php echo urlencode($sitePath);?>"><i class="icon-angleb-left"></i> 返回條目</a></span>
<div class="asidebox infobox">
	<div class="infobox-image">
		<img src="<?php echo $metainfo->image? $dataHome.$sitePath."/images/".$metainfo->image : "defaultPic.png";?>" />
	</div>

	<div class="asidebox-content infobox-infotable">
<?php
		if($IS_AUTHOR):
?>
		<a id="editMetaBtn" href="metaSetting.php?site=<?php echo urlencode($sitePath);?>"><i class="icon-cog"></i> 設定</a>
<?php
		endif;
?>
		<p class="infobox-name"><?php echo $metainfo->title;?></p>
		<p class="infobox-name"><?php echo $metainfo->etitle;?></p>
	
		<table class="infotable clearfix">
		<tbody>

<?php
		foreach($meta as $metaRow){
?>
		<tr class="infotable-row">
		<th class="infotable-row-header"><?php echo $metaRow->name;?></th>
		<td class="infotable-row-content"><?php echo $metaRow->value;?></td>
		</tr>
<?php
		}
?>
		<!--tr class="infotable-row">
		<th class="infotable-row-header">逝世</th>
		<td class="infotable-row-content">1791年12月5日（35歲）<br>
		神聖羅馬帝國 奧地利 維也納
		</td>
		</tr-->
		</tbody>
		</table>
	</div>
</div>
<div class="asidebox">
	<div class="asidebox-header">作者資訊</div>
	<div class="asidebox-content">
		<table class="infotable infotable-fillwidth clearfix">
		<tbody>

		<tr class="infotable-row">
		<th class="infotable-row-header">作者</th>
		<td class="infotable-row-content">
			<a href="user.php?u=<?php echo $metainfo->author;?>"><?php echo $metainfo->author;?></a>
		</td>
		</tr>
		<tr class="infotable-row">
		<th class="infotable-row-header">協作者</th>
		<td class="infotable-row-content"><?php echo ($metainfo->collaborator)? $metainfo->collaborator:"無";?></td>
		</tr>
		<tr class="infotable-row">
		<th class="infotable-row-header">最後編輯</th>
		<td class="infotable-row-content"><? echo $metainfo->lastEdit;?><br>(<a href="#">歷史編輯紀錄</a>)</td>
		</tr>
		</tbody>
		</table>
	</div>
</div>
</div>

<div class="main-content">

<h3>歷史編輯紀錄</h3>
<ul id="editHistoryList">
<?php
		for($i=0;$i<count($history->history);$i++){
		
			$record = $history->history[$i];
		
			$editor = $record->editor;
			$date = date("Y/m/d H:i:s",strtotime($record->date));
			
			$descStr = "<a href=\"user.php?u=$editor\">$editor</a> 於 $date ";
			switch($record->action){
			
				case 'c':
					$descStr.="建立條目";
					break;
				case 'f':
					$descStr.="建立分支版本";
					$descStr.=" (自 <a href=\"index.php?site=".$record->sAuthor."/".$record->sEid."\">".$record->sTitle."</a> by ".$record->sAuthor." )";
					break;
				case 'e':
					$descStr.="編輯條目";
					break;
				case 'r':
					$descStr.="恢復成先前版本(".$record->fromVer."版本)";
					break;
			}

			$recordStr = "<li><i class=\"icon-clock\"></i>".$descStr;
			
			if($i==count($history->history)-1)
				$recordStr.="<span class=\"curVersion-flag\">目前版本</span>";
			else if($IS_AUTHOR)
				$recordStr.= "<a class=\"recover-btn\" href=\"versionPreview.php?site=".$sitePath."&ver=".$record->date."\"><i class=\"icon-history\"></i>回復此版本</a>";
			
			$recordStr.="</li>";
			
			echo $recordStr;
		}



?>

<!--li><i class="icon-clock"></i>mikeliu32 於 2014/06/16 18:00 建立條目<span class="recover-btn"><i class="icon-history"></i>回復此版本</span></li>
<li><i class="icon-clock"></i>mikeliu32 於 2014/06/16 18:00 建立分支版本 (來源:莫札特 by gaislab)<span class="recover-btn"><i class="icon-history"></i>回復此版本</span></li>
<li><i class="icon-clock"></i>mikeliu32 於 2014/06/16 18:00 編輯條目<span class="recover-btn"><i class="icon-history"></i>回復此版本</span></li-->
 
</ul>
</div>

<?php
	endif;
?>

</main>
<script type="text/javascript">
$( document ).ready(function() {


});

</script>

</body>

</html>