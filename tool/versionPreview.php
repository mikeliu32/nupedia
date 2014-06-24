<?php
date_default_timezone_set('Asia/Taipei');

require_once('pathManage.php');

$versionDate = $_GET['ver'];

$versionDate_shown = date("Y/m/d H:i:s",strtotime($versionDate));

$metafile = $dataHome.$sitePath."/metainfo.json";
$metainfo = json_decode(file_get_contents($metafile));

$meta = $metainfo->meta;

$historyContentfile = $dataHome.$sitePath."/versions/content.json_".$versionDate;
$his_article = json_decode(file_get_contents($historyContentfile));

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

<title><?php echo $metainfo->title;?> | NuPedia</title>
<meta name="description" content="<?php echo $his_article->abstract_plain;?>"/>

<!-- Twitter Card data --> 
<meta name="twitter:card" value="summary"/>

<!-- Open Graph data --> 
<meta property="og:title" content="<?php echo $metainfo->title;?> | NuPedia"/> 
<meta property="og:type" content="article"/> 
<meta property="og:url" content="http://gaislab.cs.ccu.edu.tw/~yml101/nupedia/tool/index.php?site=<?php echo $sitePath;?>" />
<meta property="og:image" content="http://gaislab.cs.ccu.edu.tw/~yml101/nupedia/<?php echo $metainfo->image? "npdata/".$sitePath."/images/".$metainfo->image : "tool/defaultPic.png";?>" />
<meta property="og:description" content="<?php echo $his_article->abstract_plain;?>"/>
<meta property="og:site_name" content="NUPedia" />
</head>

<body>
<?php
require_once('header.php');
?>

<div class="previewMask">
	<span class="previewPrompt">正在預覽<?php echo $versionDate_shown;?>的版本，確定要還原成此版本嗎?
	<button name="recover" id="saveBtn">確定</button>
	<button name="cancel" id="exitBtn">取消</button>
	<div id="saveStatus">還原中...</div>
	</span>
</div>

<main class="main-wrapper">
<nav id="article-section-nav">
<ul>
<li><a href="#arti-header">摘要</a></li>
<?php
	foreach($his_article->sections as $section){
?>
<li><a href="#arti-sec<?php echo $section->secOrder;?>"><?php echo $section->secName;?></a></li>
<?php
	}
?>
<!--li><a href="#arti-sec2">作品</a></li-->
</ul>
</nav>


<div class="main-leftaside">
<div class="asidebox infobox">
	<div class="infobox-image">
		<img src="<?php echo $metainfo->image? $dataHome.$sitePath."/images/".$metainfo->image : "defaultPic.png";?>" />
	</div>

	<div class="asidebox-content infobox-infotable">
		<a id="editMetaBtn" href="metaSetting.php?site=<?php echo urlencode($sitePath);?>"><i class="icon-cog"></i> 設定</a>
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
		<td class="infotable-row-content"><? echo $metainfo->lastEdit;?><br>(<a href="editHistory.php?site=<?php echo urlencode($sitePath);?>">歷史編輯紀錄</a>)</td>
		</tr>
		</tbody>
		</table>
	</div>
</div>
</div>

<div class="main-content">
<article class="main-article">
<header id="arti-header">
<div>
<h1 id="article-title"><?php echo $metainfo->title;?></h1>
<a href="editArticle.php?site=<?php echo $sitePath;?>"><i class="section-header-edit"></i>[編輯]</a>
</div>

<div class="article-abstract article-section">
<?php echo $his_article->abstract;?>
</div>

</header>

<?php
	foreach($his_article->sections as $section){
?>

<section class="article-section" id="arti-sec<?php echo $section->secOrder;?>">
<div class="section-header">
<h2><?php echo $section->secName;?></h2>
<a href="editArticle.php?site=<?php echo $sitePath;?>&secID=<?php echo $section->secOrder;?>" class="section-header-edit">[編輯]</a>
</div>
<?php echo $section->content;?>
</section>

<?php
	}
?>

<!--section class="article-section" id="arti-sec2">
<div class="section-header">
<h2>作品</h2>
</div>
</section-->

</article>

</div>


</main>
<script type="text/javascript">
$( document ).ready(function() {

/***** article section nav *****/

	topGapping=46;
	artiNavbox = $("#article-section-nav");
	var artiNavbox_pos = artiNavbox.position();
	
	artiNavItems = artiNavbox.find("a");

	if(artiNavItems.first()){
		artiNavItems.first().addClass("active");
	}
	
	artiSecList = artiNavItems.map(function(){
		var item = $($(this).attr("href"));
		
		if(item.length){
			return item;
		}
	});

	
	artiNavItems.click(function(e){
	
		var href = $(this).attr("href");
		scrollTo = (href ==="#")? 0: $(href).offset().top+1;

		$('html, body').stop().animate(
		{scrollTop: scrollTo
		}, 300);

		e.preventDefault();
	});

	
	var lastArtiSecId;

	$(window).scroll(function(){

		var window_pos = $(window).scrollTop()+topGapping;

		if(window_pos>=94){
			artiNavbox.find("ul").addClass("fixed");
		}
		else{
			artiNavbox.find("ul").removeClass("fixed");
		}

		var curArtiSec = artiSecList.map(function(){
			if($(this).offset().top<window_pos){
				return this;
			}
		});

		curArtiSec = curArtiSec[curArtiSec.length-1];


		var curArtiSecId = curArtiSec && curArtiSec.length? curArtiSec[0].id : "";


		if(lastArtiSecId !== curArtiSecId){
			lastArtiSecId = curArtiSecId;

			if(curArtiSecId.length!=0)
				artiNavItems.removeClass("active").filter("[href=#"+curArtiSecId+"]").addClass("active");
		}

	});
	
	$('#saveBtn').click(function(e){
		e.preventDefault();
		$("#saveStatus").html("還原中...")
		$("#saveStatus").show();
		$("#saveBtn").prop('disabled', true);
		$("#exitBtn").prop('disabled', true);

		 		
		var request = $.ajax({
		  url: "version_process.php?site=<?php echo $sitePath;?>",
		  type: "POST",
		  data: { ver : '<?php echo $versionDate;?>' },
		  dataType: "json"
		});
		 
		request.done(function( jData ) {

			if(jData.status=='ok'){
				$("#saveStatus").html("還原成功! 跳轉中...");
				
				 window.setTimeout(function(){
					window.location.href = "index.php?site=<?php echo $sitePath;?>";
				}, 3000);
			}
			else{
				$("#saveStatus").html("還原錯誤!請重試");
				$("#saveBtn").prop('disabled', false);
				$("#exitBtn").prop('disabled', false);		
			}
		});
		 
		request.fail(function( jqXHR, textStatus ) {
		  alert( "Request failed: " + textStatus );
		});
		
		
	});
	
	$('#exitBtn').click(function(e){
		e.preventDefault();
		window.location = 'version.php?site=<?php echo $sitePath;?>'
	
	});



});

</script>

</body>

</html>