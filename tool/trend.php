<?php
include_once('inc/auth.php');

$resultAry=array();
	
$srObj = getHotEntries(10);
	
$resultAry = $srObj->hits->hits;


?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="css/nupedia_all.css" type="text/css">
<link rel="stylesheet" href="css/normalize.css" type="text/css">
<link rel="stylesheet" href="css/fonts.css" type="text/css">
<link rel="stylesheet" href="css/searchpage.css" type="text/css">
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
	<div class="tagHeader">熱門條目</div>
	<div id="searchResultPanel">
		<ul>
<?php
			foreach($resultAry as $sr){
				$entry = $sr->_source;
				$eid = $entry->eid;
				$author = $entry->author;
				$image = $entry->image;
				$title = $entry->title;
				$tags = $entry->tag;
				$lastEdit = $entry->lastEdit;
				$abs_plain = mb_strimwidth($entry->abstract_plain, 0, 300, "...", "UTF-8");
				
				$site = $author."/".$eid;
				$imagePath = $image? "../npdata/".$site."/images/".$image : "defaultPic.png";
?>
			<li class="clearfix">
				<div class="sr-image" style="background-image:url('<?php echo $imagePath;?>');"></div>
				<div class="sr-info">
					<a href="index.php?site=<?php echo $site;?>" class="sr-info-title"><?php echo $title;?></a>
					<span class="sr-info-extra">作者 <a href="user.php?u=<?php echo $author;?>"><?php echo $author;?></a> ．最後編輯 <?php echo $lastEdit;?></span>
					<p class="sr-info-abs"><?php echo $abs_plain;?></p>
				
					<div class="sr-tags">
					<?php
						if($tags){
							foreach($tags as $tag)
								echo "<a class=\"tag\" href=\"tags.php?tag=".urlencode($tag)."\">$tag</a>";
						}
					?>
					</div>
				</div>

			</li>
<?php
			}
?>
	

		</ul>
		<?php
			if(count($resultAry)<=0)
				echo "<span class=\"list-noitem\">沒有資料 :(</span>";
		?>
	</div>
</main>
<script type="text/javascript">
$( document ).ready(function() {





});

</script>

</body>

</html>

<?php

function getHotEntries($size){

$elasticUrl_search = "http://gaisq.cs.ccu.edu.tw:9200/nupedia/entry/_search?q=(isVisible:1)&sort=viewCount:desc,favCount:desc";

$searchUrl = $elasticUrl_search."&size=".$size;

$ch = curl_init($searchUrl);

curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);

curl_close($ch);  // Seems like good practice

$resultJObj = json_decode($result);

return $resultJObj;
}


?>