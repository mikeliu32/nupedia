<?php
include_once('inc/auth.php');
include_once('pathManage.php');
include_once('inc/action_viewCount.php');

if($IS_ENTRY_EXIST){

	$metafile = $dataHome.$sitePath."/metainfo.json";
	$metainfo = json_decode(file_get_contents($metafile));

	$meta = $metainfo->meta;

	$contentfile = $dataHome.$sitePath."/content.json";
	$article = json_decode(file_get_contents($contentfile));

	//add view count
	addViewCount($entryID);
	
	if($IS_LOGIN){
		$IS_AUTHOR = isAuthor($metainfo->author);
		if($metainfo->collaborator)
			$IS_COLLAB = in_array($USER_ID, $metainfo->collaborator);
	}
}
else{
	header("Location: error.php");
	die();
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="css/normalize.css" type="text/css">
<link rel="stylesheet" href="css/nupedia_all.css" type="text/css">
<link rel="stylesheet" href="css/nupedia.css" type="text/css">
<link rel="stylesheet" href="css/fonts.css" type="text/css">

<link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" type="text/css">
<!--script src="http://code.jquery.com/jquery-1.10.2.min.js"></script-->
<script src="js/jquery-1.11.0.min.js"></script>

<title><?php echo $metainfo->title;?> | NuPedia</title>
<meta name="description" content="<?php echo $article->abstract_plain;?>"/>
<!-- Twitter Card data --> 
<meta name="twitter:card" value="summary"/>
<!-- Open Graph data --> 
<meta property="og:title" content="<?php echo $metainfo->title;?> | NuPedia"/> 
<meta property="og:type" content="article"/> 
<meta property="og:url" content="http://gaislab.cs.ccu.edu.tw/~yml101/nupedia/tool/index.php?site=<?php echo $sitePath;?>" />
<meta property="og:image" content="http://gaislab.cs.ccu.edu.tw/~yml101/nupedia/<?php echo $metainfo->image? "npdata/".$sitePath."/images/".$metainfo->image : "tool/defaultPic.png";?>" />
<meta property="og:description" content="<?php echo $article->abstract_plain;?>"/>
<meta property="og:site_name" content="NUPedia" />
</head>

<body>
<!-- FB JS Library-->
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.0";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<?php
require_once('header.php');
?>

<main class="main-wrapper">

<nav id="article-section-nav">
<ul>
<li><a href="#arti-header">摘要</a></li>
<?php
	foreach($article->sections as $section){
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
		if($meta){
			foreach($meta as $metaRow){
?>
		<tr class="infotable-row">
		<th class="infotable-row-header"><?php echo $metaRow->name;?></th>
		<td class="infotable-row-content"><?php echo $metaRow->value;?></td>
		</tr>
<?php
			}
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
		<td class="infotable-row-content">

		<?php 
			if($metainfo->collaborator){
				for($i=0; $i<count($metainfo->collaborator) ;$i++){
					$collab = $metainfo->collaborator[$i];
					echo '<a href="user.php?u='.$collab.'">'.$collab.'</a>';
					
					if($i!=count($metainfo->collaborator)-1)
						echo ', ';
				}
			}
			else
				echo "無";
		?>
		</td>
		</tr>
		<tr class="infotable-row">
		<th class="infotable-row-header">最後編輯</th>
		<td class="infotable-row-content"><? echo $metainfo->lastEdit;?><br>(<a href="version.php?site=<?php echo urlencode($sitePath);?>">歷史編輯紀錄</a>)</td>
		</tr>
		</tbody>
		</table>
	</div>
</div>
<div class="asidebox">
	<div class="asidebox-header">標籤</div>
	<div class="asidebox-content">
<?php
	if($metainfo->tag){
	foreach($metainfo->tag as $tag)
		echo "<a class=\"tag\" href=\"tags.php?tag=".urlencode($tag)."\">$tag</a>";
	}
	else
		echo "無";
?>
	</div>
</div>
<div class="asidebox">
	<div class="asidebox-header">相關條目</div>
	<div class="asidebox-content">
		<ul id="relatedTermList">
		<li><a href="#">貝多芬</a></li>
		<li><a href="#">海頓</a></li>
		<li><a href="#">奧地利</a></li>
		<li><a href="#">古典音樂</a></li>
		<li><a href="#">魔笛</a></li>
		<li><a href="#">交響樂</a></li>
		</ul>
	</div>
</div>
<div class="asidebox">
	<div class="asidebox-header">熱門條目</div>
	<div class="asidebox-content">
		<ul id="hotEntryList">
		<li><a href="#">太陽花學運</a></li>
		<li><a href="#">馬英九</a></li>
		<li><a href="#">中華民國憲法</a></li>
		<li><a href="#">兩岸服貿協議</a></li>
		<li><a href="#">黑色島國</a></li>
		</ul>
	</div>
</div>
</div>

<div class="main-content">
<article class="main-article">
<header id="arti-header">
<h1 id="article-title"><?php echo $metainfo->title;?></h1>
<?php
		if($IS_AUTHOR || $IS_COLLAB):
?>
<a href="editArticle.php?site=<?php echo $sitePath;?>"><i class="section-header-edit"></i>[編輯]</a>
<?php
		endif;
?>
<div class="socialPanel">
<a id="addFavBtn" class="socialBtn">收藏</a>
<?php
	if($metainfo->isForkable){
?>
<a class="socialBtn" href="forkEntry.php?site=<?php echo $sitePath;?>">建立分支條目</a>
<?php
	}
?>
<!-- FB Share Btn -->
<div class="fb-like" data-href="http://gaislab.cs.ccu.edu.tw/~yml101/nupedia/tool/index.php?site=<?php echo $sitePath;?>" data-layout="button_count" data-action="like" data-show-faces="true" data-share="true"></div>
</div>

<div class="article-abstract article-section">
<?php echo $article->abstract;?>
</div>

</header>

<?php
	foreach($article->sections as $section){
?>

<section class="article-section" id="arti-sec<?php echo $section->secOrder;?>">
<div class="section-header">
<h2><?php echo $section->secName;?></h2>
<?php
		if($IS_AUTHOR || $IS_COLLAB):
?>
<a href="editArticle.php?site=<?php echo $sitePath;?>&secID=<?php echo $section->secOrder;?>" class="section-header-edit">[編輯]</a>
<?php
		endif;
?>
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

<section class="video-section">
<div class="section-header">
<h2>相關影音</h2>
<a href="#" class="section-header-seemore">查看所有影音</a>
<!--a href="#" class="section-header-expand">[+展開]</a-->
</div>
<div id="video-scroller" class="video-scroller">
	<!--div class="video-wrapper">
	<ul class="videoboxlist">
	<li class="videobox">
	<img src="https://i1.ytimg.com/vi/OipS7HLPNK4/mqdefault.jpg"/>
	<div class="videobox-info">
		<a href="#">Mozart Concert</a>
		<p>this is the video description</p>
	</div>
	</li>
	<li class="videobox">
	<img src="https://i1.ytimg.com/vi/QtFZC658RP8/mqdefault.jpg"/>
	<div class="videobox-info">
		<a href="#">Mozart Concert</a>
		<p>this is the video description this is the video description this is the video description this is the video description</p>
	</div>
	</li>
	<li class="videobox">
	<img src="https://i1.ytimg.com/vi/gTEn6SRVAE4/mqdefault.jpg"/>
	<div class="videobox-info">
		<a href="#">Mozart Concert演奏會</a>
		<p>中文影片描述，中文影片描述，中文影片描述，中文影片描述</p>
	</div>
	</li>
	<li class="videobox">
	<img src="https://i1.ytimg.com/vi/OipS7HLPNK4/mqdefault.jpg"/>
	<div class="videobox-info">
		<a href="#">Mozart Concert</a>
		<p>this is the video description</p>
	</div>
	</li>
	<li class="videobox">
	<img src="https://i1.ytimg.com/vi/QtFZC658RP8/mqdefault.jpg"/>
	<div class="videobox-info">
		<a href="#">Mozart Concert</a>
		<p>this is the video description this is the video description this is the video description this is the video description</p>
	</div>
	</li>
	<li class="videobox">
	<img src="https://i1.ytimg.com/vi/gTEn6SRVAE4/mqdefault.jpg"/>
	<div class="videobox-info">
		<a href="#">Mozart Concert演奏會</a>
		<p>中文影片描述，中文影片描述，中文影片描述，中文影片描述</p>
	</div>
	</li>
	<li class="videobox">
	<img src="https://i1.ytimg.com/vi_webp/Xm-ddyuELgs/mqdefault.webp"/>
	<div class="videobox-info">
		<a href="#">Mozart Concert</a>
		<p>this is the video description</p>
	</div>
	</li>
	<li class="videobox">
	<img src="https://i1.ytimg.com/vi_webp/_0mIhjhUnjQ/mqdefault.webp"/>
	<div class="videobox-info">
		<a href="#">Mozart Concert</a>
		<p>this is the video description</p>
	</div>
	</li>
	<li class="videobox">
	<img src="https://i1.ytimg.com/vi/ue9I3eEySho/mqdefault.jpg"/>
	<div class="videobox-info">
		<a href="#">Mozart Concert</a>
		<p>this is the video description</p>
	</div>
	</li>
	<li class="videobox">
	<img src="https://i1.ytimg.com/vi/OipS7HLPNK4/mqdefault.jpg"/>
	<div class="videobox-info">
		<a href="#">Mozart Concert</a>
		<p>this is the video description</p>
	</div>
	</li>
	<li class="videobox">
	<img src="https://i1.ytimg.com/vi/QtFZC658RP8/mqdefault.jpg"/>
	<div class="videobox-info">
		<a href="#">Mozart Concert</a>
		<p>this is the video description this is the video description this is the video description this is the video description</p>
	</div>
	</li>
	<li class="videobox">
	<img src="https://i1.ytimg.com/vi_webp/Xm-ddyuELgs/mqdefault.webp"/>
	<div class="videobox-info">
		<a href="#">Mozart Concert</a>
		<p>this is the video description</p>
	</div>
	</li>
	<li class="videobox">
	<img src="https://i1.ytimg.com/vi_webp/_0mIhjhUnjQ/mqdefault.webp"/>
	<div class="videobox-info">
		<a href="#">Mozart Concert</a>
		<p>this is the video description</p>
	</div>
	</li>
	<li class="videobox">
	<img src="https://i1.ytimg.com/vi/ue9I3eEySho/mqdefault.jpg"/>
	<div class="videobox-info">
		<a href="#">Mozart Concert</a>
		<p>this is the video description</p>
	</div>
	</li>
	<li class="videobox">
	<img src="https://i1.ytimg.com/vi/OipS7HLPNK4/mqdefault.jpg"/>
	<div class="videobox-info">
		<a href="#">Mozart Concert</a>
		<p>this is the video description</p>
	</div>
	</li>
	<li class="videobox">
	<img src="https://i1.ytimg.com/vi/QtFZC658RP8/mqdefault.jpg"/>
	<div class="videobox-info">
		<a href="#">Mozart Concert</a>
		<p>this is the video description this is the video description this is the video description this is the video description</p>
	</div>
	</li>
	</ul>
	</div>
	<div class="video-scroller-btn video-scroller-btn-left video-scroller-btn-deactive">&lt;</div>
	<div class="video-scroller-btn video-scroller-btn-right">&gt;</div-->
</div>

</section>

<section class="photo-section">
<div class="section-header">
<h2>相關圖片</h2>
</div>
<div class="video-scroller">
	<!--div class="video-wrapper">
	<ul class="videoboxlist">
	<li class="videobox">
	<img src="https://i1.ytimg.com/vi/OipS7HLPNK4/mqdefault.jpg"/>
	<div class="videobox-info">
		<a href="#">Mozart Concert</a>
		<p>this is the video description</p>
	</div>
	</li>
	<li class="videobox">
	<img src="https://i1.ytimg.com/vi/QtFZC658RP8/mqdefault.jpg"/>
	<div class="videobox-info">
		<a href="#">Mozart Concert</a>
		<p>this is the video description this is the video description this is the video description this is the video description</p>
	</div>
	</li>
	<li class="videobox">
	<img src="https://i1.ytimg.com/vi/gTEn6SRVAE4/mqdefault.jpg"/>
	<div class="videobox-info">
		<a href="#">Mozart Concert演奏會</a>
		<p>中文影片描述，中文影片描述，中文影片描述，中文影片描述</p>
	</div>
	</li>
	<li class="videobox">
	<img src="https://i1.ytimg.com/vi_webp/Xm-ddyuELgs/mqdefault.webp"/>
	<div class="videobox-info">
		<a href="#">Mozart Concert</a>
		<p>this is the video description</p>
	</div>
	</li>
	<li class="videobox">
	<img src="https://i1.ytimg.com/vi_webp/_0mIhjhUnjQ/mqdefault.webp"/>
	<div class="videobox-info">
		<a href="#">Mozart Concert</a>
		<p>this is the video description</p>
	</div>
	</li>
	<li class="videobox">
	<img src="https://i1.ytimg.com/vi/ue9I3eEySho/mqdefault.jpg"/>
	<div class="videobox-info">
		<a href="#">Mozart Concert</a>
		<p>this is the video description</p>
	</div>
	</li>
	<li class="videobox">
	<img src="https://i1.ytimg.com/vi/OipS7HLPNK4/mqdefault.jpg"/>
	<div class="videobox-info">
		<a href="#">Mozart Concert</a>
		<p>this is the video description</p>
	</div>
	</li>
	<li class="videobox">
	<img src="https://i1.ytimg.com/vi/QtFZC658RP8/mqdefault.jpg"/>
	<div class="videobox-info">
		<a href="#">Mozart Concert</a>
		<p>this is the video description this is the video description this is the video description this is the video description</p>
	</div>
	</li>
	</ul>
	</div>
	<div class="video-scroller-btn video-scroller-btn-left">&lt;</div>
	<div class="video-scroller-btn video-scroller-btn-right">&gt;</div-->
</div>

</section>
<!-- FB comment plugin -->
<div class="fb-comments" data-href="http://gaislab.cs.ccu.edu.tw/~yml101/nupedia/tool/index.php?site=<?php echo $sitePath;?>" data-width="100%" data-numposts="5" data-colorscheme="light"></div>

<div class="selectTextMenu">
	<ul>
		<li>引用</li>
		<li>分享</li>
	</ul>
</div>
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

/***** add Favorite Btn *****/
<?php
if(!$IS_LOGIN):
?>
	$(".socialBtn").click(function(e){
		e.preventDefault();
		alert("請先登入!");
	});
<?php
else:
?>
	$("#addFavBtn").hide();
	checkFavorite();
	
	$("#addFavBtn").click(function(e){

		var isFav = $(this).data('isFavorite');
		
		if(isFav)
			removeFavorite();
		else 
			addFavorite();
			
		e.preventDefault();
	});
	
	
	function checkFavorite(){
	
		var request = $.ajax({
		  url: "inc/action_favorite.php?action=c",
		  type: "POST",
		  data: { eid: '<?php echo $entryID;?>'},
		  dataType: "json"
		});
		 
		request.done(function( jData ) {

			if(jData.isFavorite==true){
				$("#addFavBtn").html("已收藏");
				$("#addFavBtn").data("isFavorite",true);
				$("#addFavBtn").show();

			}
			else{
				$("#addFavBtn").html("收藏");
				$("#addFavBtn").data("isFavorite",false);
				$("#addFavBtn").show();	
			}
		});
		 
		request.fail(function( jqXHR, textStatus ) {
		  alert( "Request failed: " + textStatus );
		});
		
	}
	
	function addFavorite(){
	
		var request = $.ajax({
		  url: "inc/action_favorite.php?action=a",
		  type: "POST",
		  data: { eid: '<?php echo $entryID;?>'},
		  dataType: "json"
		});
		 
		request.done(function( jData ) {
		
			if(jData.status=="ok"){
				$("#addFavBtn").html("已收藏");
				$("#addFavBtn").data("isFavorite",true);
			}
		});
		 
		request.fail(function( jqXHR, textStatus ) {
		  alert( "Request failed: " + textStatus );
		});
		
	}
	
	function removeFavorite(){
	
		var request = $.ajax({
		  url: "inc/action_favorite.php?action=r",
		  type: "POST",
		  data: { eid: '<?php echo $entryID;?>'},
		  dataType: "json"
		});
		 
		request.done(function( jData ) {
		
			if(jData.status=="ok"){
				$("#addFavBtn").html("收藏");
				$("#addFavBtn").data("isFavorite",false);
			}
		});
		 
		request.fail(function( jqXHR, textStatus ) {
		  alert( "Request failed: " + textStatus );
		});
		
	}
<?php
endif;
?>
/***** get hot entry list *****/
	getHotList();
	function getHotList(){
	
		var request = $.ajax({
		  url: "inc/action_getHotEntries.php?size=5",
		  type: "GET",
		  dataType: "json"
		});
		 
		request.done(function( jData ) {
		
			if(jData.length==0)
				$("#hotEntryList").html("無");
			
			else{
				var hotlistHtml = "";
				for(var i = 0 ; i<jData.length ;i++)
					hotlistHtml+="<li><a href=\"index.php?site="+jData[i].sitePath+"\">"+jData[i].title+"</a></li>";
				
				$("#hotEntryList").html(hotlistHtml);

			}

		});
		 
		request.fail(function( jqXHR, textStatus ) {
		  alert( "Request failed: " + textStatus );
		});
		
	}
	
/***** get related term list *****/
	getRelatedTermList('<?php echo $metainfo->title;?>');
	function getRelatedTermList(keyword){
	
		var request = $.ajax({
		  url: "inc/action_getRelatedTerm.php",
		  type: "GET",
		  data: { keyword: keyword, size: 10 },
		  dataType: "json"
		});
		 
		request.done(function( jData ) {
		
			if(jData.count==0)
				$("#relatedTermList").html("無");
			
			else{
				var relatedTermHtml = "";
				for(var i = 0 ; i<jData.results.length ;i++)
					relatedTermHtml+="<li><a href=\"showTopic.php?keyword="+jData.results[i].relatedword+"\">"+jData.results[i].relatedword+"</a></li>";
				
				$("#relatedTermList").html(relatedTermHtml);

			}

		});
		 
		request.fail(function( jqXHR, textStatus ) {
		  alert( "Request failed: " + textStatus );
		});
		
	}
	
/***** video scroller *****/


	video_scroller = $("#video-scroller");
	video_wrapper = video_scroller.find(".video-wrapper");
	videoboxlist = video_scroller.find(".videoboxlist");
	
	videobox_count = videoboxlist.children().length; 
	videoboxlist.width(Math.ceil(videobox_count/2) * 282);
	
	videoPerPage = Math.floor(video_wrapper.width()/282);
	totalOffset = Math.ceil(videobox_count/2);
	curOffset=0;
	
	leftBtn = video_scroller.find(".video-scroller-btn-left");
	rightBtn = video_scroller.find(".video-scroller-btn-right");
	
	leftBtn.click(function() {
		
		if(curOffset>0){
			offsetLeft = curOffset;
			offsetToMove = (offsetLeft>=videoPerPage)? videoPerPage : offsetLeft;
			videoboxlist.css("margin-left", (curOffset-offsetToMove)*(-282));
			curOffset-=offsetToMove;
			checkScrollerBtn();
		}
		
	});

	rightBtn.click(function() {

		if(curOffset+videoPerPage<totalOffset){
			offsetLeft = totalOffset-(curOffset+videoPerPage);
			offsetToMove = (offsetLeft>=videoPerPage)? videoPerPage : offsetLeft;
			videoboxlist.css("margin-left", (curOffset+offsetToMove)*(-282));
			curOffset+=offsetToMove;
			checkScrollerBtn();
		}

	});	

	function checkScrollerBtn(){
		
		if(curOffset==0){
			leftBtn.addClass("video-scroller-btn-deactive");
			rightBtn.removeClass("video-scroller-btn-deactive");
		}
		else if(curOffset+videoPerPage>=totalOffset){
			rightBtn.addClass("video-scroller-btn-deactive");
			leftBtn.removeClass("video-scroller-btn-deactive");
		}
		else{
			leftBtn.removeClass("video-scroller-btn-deactive");
			rightBtn.removeClass("video-scroller-btn-deactive");
		}
	}
	
/****select text********/


	function getSelected() {

		if(window.getSelection){
			return window.getSelection();
		}
		else if(document.getSelection){
			return document.getSelection();
		}
		else {
			var selection = document.selection && document.selection.createRange();

			if(selection.text){
				return selection.text;
			}
			
			return false;
		}

		return false;
	}
	
	 $('.main-article').mousedown(function(e) {
	    $('.selectTextMenu').hide();
		mouseDownX = e.pageX;
		mouseDownY = e.pageY;
			console.log(e.pageY, e.pageX);

	 });

	
	 $('.main-article').mouseup(function(e) {

        var selection = getSelected();
		
        if(selection && selection.anchorOffset!=selection.focusOffset) {
		console.log(selection);
	console.log(e.pageY, e.pageX);
        $('.selectTextMenu').css({

        top: mouseDownY-140, //offsets

        left: mouseDownX-200 //offsets

        }).fadeIn();

        }

    });



});

</script>

</body>

</html>