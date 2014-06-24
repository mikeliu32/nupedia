<?php
include_once('inc/auth.php');

include_once('inc/db_class.php');
include_once('inc/db_config.php');

$db = new DB();
$db->connect_db($_DB['host'],$_DB['username'],$_DB['password'],$_DB['dbname']);


$resultAry=array();

$userId = null;

if(isset($_GET['u'])&&$_GET['u']!=NULL){
	$userId = $_GET['u'];
	$IS_AUTHOR = isAuthor($userId);
}

else if($IS_LOGIN){
	$userId = $USER_ID;
	$IS_AUTHOR = true;
}


if($userId!=null){
	//check if user exist
	$db->query("SELECT * from user WHERE userName='$userId';");
	$result = $db->fetch_array();
	
	if(count($result)){
		$userEntryObj = getUserEnty($userId);
		$userColabObj = getUserColab($userId);
	
		$resultAry['entry'] = $userEntryObj->hits->hits;	
		$resultAry['colab'] = $userColabObj->hits->hits;	
	}
	else{
		header("Location: error.php");
		die();
	}
}
else{
	header("Location: login.php");
	die();
}

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="css/nupedia_all.css" type="text/css">
<link rel="stylesheet" href="css/normalize.css" type="text/css">
<link rel="stylesheet" href="css/fonts.css" type="text/css">
<link rel="stylesheet" href="css/userpage.css" type="text/css">
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
	
	<div id="leftWrap">
		<div class="leftbox userinfo">
			<div class="userpic"></div>
			<div class="username"><?php echo $userId;?></div>
<?php	
		if ($IS_AUTHOR) :
?>
			<ul class="userControls">
				<li><a href="addEntry.php" class="active">新增知識</a></li>
				<li><a href="inc/login_auth.php?action=o">登出</a></li>
			</ul>
<?php
		endif;
?>
		</div>
	</div>

	<div id="rightWrap">
	<ul id="entryTypeTab">
		<li><a href="#myEntry" class="active">我的知識 (<?php echo count($resultAry['entry']);?>)</a></li>
		<li><a href="#colabEntry">協作知識 (<?php echo count($resultAry['colab']);?>)</a></li>
	</ul>
	
	
	<div id="myEntry" class="entrylist">
		<ul>
<?php
			foreach($resultAry['entry'] as $sr){
				$entry = $sr->_source;
				$eid = $entry->eid;
				$author = $entry->author;
				$image = $entry->image;
				$title = $entry->title;
				$lastEdit = $entry->lastEdit;
				$abs_plain = mb_strimwidth($entry->abstract_plain, 0, 300, "...", "UTF-8");
				
				$site = $author."/".$eid;
				$imagePath = $image? "../npdata/".$site."/images/".$image : "defaultPic.png";
?>
			<li class="clearfix">
				<div class="sr-image" style="background-image:url('<?php echo $imagePath;?>');"></div>
				<div class="sr-info">
					<a href="index.php?site=<?php echo $site;?>" class="sr-info-title"><?php echo $title;?></a>
					<span class="sr-info-extra">作者 <?php echo $author;?> ．最後編輯 <?php echo $lastEdit;?></span>
					<p class="sr-info-abs"><?php echo $abs_plain;?></p>
				</div>
			</li>
<?php
			}
?>
	

		</ul>
		<?php
			if(count($resultAry['entry'])<=0)
				echo "<span class=\"list-noitem\">沒有資料 :(</span>";
		?>
	</div>
	<div id="colabEntry" class="entrylist">
		<ul>
<?php
			foreach($resultAry['colab'] as $sr){
				$entry = $sr->_source;
				$eid = $entry->eid;
				$author = $entry->author;
				$image = $entry->image;
				$title = $entry->title;
				$lastEdit = $entry->lastEdit;
				$abs_plain = mb_strimwidth($entry->abstract_plain, 0, 300, "...", "UTF-8");
				
				$site = $author."/".$eid;
				$imagePath = $image? "../npdata/".$site."/images/".$image : "defaultPic.png";
?>
			<li class="clearfix">
				<div class="sr-image" style="background-image:url('<?php echo $imagePath;?>');"></div>
				<div class="sr-info">
					<a href="index.php?site=<?php echo $site;?>" class="sr-info-title"><?php echo $title;?></a>
					<span class="sr-info-extra">作者 <?php echo $author;?> ．最後編輯 <?php echo $lastEdit;?></span>
					<p class="sr-info-abs"><?php echo $abs_plain;?></p>
				</div>
			</li>
<?php
			}
?>
	

		</ul>
		<?php
			if(count($resultAry['colab'])<=0)
				echo "<span class=\"list-noitem\">沒有資料 :(</span>";
		?>
	</div>
	
	</div>

</main>
<script type="text/javascript">
$( document ).ready(function() {

	$("#myEntry").show();

	$("#entryTypeTab a").click(function(e){
		e.preventDefault();
		var targetTab = $(this).attr("href");
		
		$("#entryTypeTab a").removeClass("active").filter("[href="+targetTab+"]").addClass("active");
		$(".entrylist").hide();
		$(targetTab).show();

	});



});

</script>

</body>

</html>

<?php

function getUserEnty($userID){

$elasticUrl_search = "http://gaisq.cs.ccu.edu.tw:9200/nupedia/entry/_search?q=author:";

$searchUrl = $elasticUrl_search.$userID;

$ch = curl_init($searchUrl);

curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);

curl_close($ch);  // Seems like good practice

$resultJObj = json_decode($result);

return $resultJObj;
}

function getUserColab($userID){

$elasticUrl_search = "http://gaisq.cs.ccu.edu.tw:9200/nupedia/entry/_search?q=collaborator:";

$searchUrl = $elasticUrl_search.$userID;

$ch = curl_init($searchUrl);

curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);

curl_close($ch);  // Seems like good practice

$resultJObj = json_decode($result);

return $resultJObj;
}

?>