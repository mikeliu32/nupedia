<?php
include_once('inc/auth.php');

if(!$IS_LOGIN){
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
<link rel="stylesheet" href="css/addEntryPage.css" type="text/css">
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
	<div id="entryname-wrap">
	<h3>條目名稱</h3>
	<input id="entryname-input" type="text"></input>
	</div>
	
	<div id="typeSelectWrap">
		<h3>選擇條目類別</h3>
		<ul id="typeSelect" class="typelist">
			<li type="0">人物</li>
			<li type="1">事件</li>
			<li type="2">地理</li>
			<li type="3">文學</li>
			<li type="4">藝術</li>
			<li type="5">應用科學</li>
			<li type="6">自然科學</li>
			<li type="7">語言</li>
			<li type="8">人文</li>
			<li type="9">社會</li>
			<li type="10">宗教</li>
			<li type="11">心理</li>
			<li type="12">資訊</li>
		</ul>

		<ul id="type-0" class="typelist subtypelist">
			<li subtype="0">音樂家</li>
			<li subtype="1">科學家</li>
			<li subtype="2">哲學家</li>
			<li subtype="3">作家</li>
			<li subtype="4">政治人物</li>
			<li subtype="5">宗教人物</li>
			<li subtype="6">歷史人物</li>
			<li subtype="7">體育人物</li>
			<li subtype="8">企業家</li>
			<li subtype="9">演藝人物</li>
		</ul>
		<ul id="type-1" class="typelist subtypelist">
			<li subtype="0">一般事件</li>
			<li subtype="1">歷史事件</li>
			<li subtype="2">新聞事件</li>
			<li subtype="3">軍事事件</li>
		</ul>
		<ul id="type-2" class="typelist subtypelist">
			<li subtype="0">國家</li>
			<li subtype="1">地理位置/區域</li>
			<li subtype="2">地形</li>
			<li subtype="3">地標</li>
		</ul>
	</div>
	<div id="footerBtns">
	<a href="#" id="saveBtn" class="footerBtn save">建立條目</a>
	<a href="#" id="exitBtn" class="footerBtn cancel">離開</a>
	</div>
</main>
<script type="text/javascript">
$( document ).ready(function() {

selectedType = -1;
selectedSubType = -1;
$("#typeSelect li").click(function(e){

	var preSelectedType = selectedType;
	selectedType = $(this).attr("type");
	
	if(preSelectedType!=selectedType){
		$(".subtypelist li").removeClass("selected");
		selectedSubType = -1;
	}
	$("#typeSelect li").removeClass("selected");
	$(this).addClass("selected");
	
	$(".subtypelist").hide();
	$("#type-"+selectedType).show();

});

$(".subtypelist li").click(function(e){

	selectedSubType = $(this).attr("subtype");
	
	$(".subtypelist li").removeClass("selected");
	
	$(this).addClass("selected");
	
});

$("#saveBtn").click(function(e){
	e.preventDefault();
	
	var newEntryTitle = $("#entryname-input").val();
	
	var request = $.ajax({
	  url: "addEntry_process.php",
	  type: "GET",
	  data: { entryTitle : newEntryTitle, type:2, subtype: 1 },
	  dataType: "json"
	});
	 
	request.done(function( jData ) {

		if(jData.status=='ok'){

			e.preventDefault();
			window.location = 'index.php?site='+jData.redirectEntrySite;
		}
		else{

		}
	});
	 
	request.fail(function( jqXHR, textStatus ) {
	  alert( "Request failed: " + textStatus );
	});	

	
});

});

</script>

</body>

</html>