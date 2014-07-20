<?php
include_once('inc/auth.php');
require_once('pathManage.php');


if($IS_ENTRY_EXIST){
	$metafile = $dataHome.$sitePath."/metainfo.json";
	$metainfo = json_decode(file_get_contents($metafile));

	$IS_AUTHOR = isAuthor($metainfo->author);
	
	if($metainfo->collaborator){
		$IS_COLLAB = in_array($USER_ID, $metainfo->collaborator);
	}
	
	if(!$IS_AUTHOR && !$IS_COLLAB){
		header("Location: error.php");
		die();
	}
	
	$meta = $metainfo->meta;

	$contentfile = $dataHome.$sitePath."/content.json";
	$article = json_decode(file_get_contents($contentfile));

	
	$filePath = $dataHome.$sitePath."/files/";
	
	if(isset($_GET['secID'])){
		$selectedSecID = $_GET['secID']; 
	}
	else{
		$selectedSecID =0;
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
<link rel="stylesheet" href="css/edit.css" type="text/css">
<link rel="stylesheet" href="css/fonts.css" type="text/css">
<link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" type="text/css">
<!--script src="http://code.jquery.com/jquery-1.10.2.min.js"></script-->
<script src="js/jquery-1.11.0.min.js"></script>
<script src="js/jquery-ui-1.10.4.custom.min.js"></script>

<!-- 載入ooki編輯器： -->
<link href="ooki/ooki/tiny_mce/plugins/plugin_src.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="ooki/ooki/tiny_mce/plugins/plugin_src.js"></script>
<script type="text/javascript" src="ooki/ooki/tiny_mce/tiny_mce.js"></script>

<script src="js/jquery.collagePlus.min.js"></script>
<script src="js/jquery.removeWhitespace.min.js"></script>

<title>NuPedia-Edit</title>
</head>

<body>
<div id="edit-Wrapper">
<div id="leftPanelArea">
<div id="leftPanel">
<div class="panelHeader">
<h3>編輯條目內文</h3>
<div class="panelHeader-title">條目名稱</div>
<p><?php echo $metainfo->title;?></p>
<div class="panelHeader-title">作者</div>
<p><?php echo $metainfo->author;?></p>
<div class="panelHeader-title">大綱
<a href="#" id="addSectionBtn" class="panelHeader-title-right">+新增</a>
<span class="hint-invert asBlock">拖曳以改變大綱順序</span>
</div>
</div>

<ul id="sectionTabs">

<li class="disabledDrag"><a href="#arti-sec0">摘要</a></li>
<?php
	foreach($article->sections as $section){
?>
<li><a href="#arti-sec<?php echo $section->secOrder;?>"><?php echo $section->secName;?></a></li>
<?php
	}
?>

<!--li><a href="#arti-sec2">作品</a></li-->
</ul>
</div>
<div id="leftPanelBtns">
	<span id="saveStatus" class="hint-invert asBlock"></span>
	<a href="#" id="saveBtn" class="leftPanelBtn save">儲存</a>
	<a href="#" id="exitBtn" class="leftPanelBtn cancel">離開</a>
</div>
</div>
<div id="contentArea">
<div id="arti-sec0" class="sectionContent">
<h2>[摘要]</h2>
<textarea id="sec0" name="sec0" class="mceEditor">
<?php echo $article->abstract;?>
</textarea>
</div>

<?php
	foreach($article->sections as $section){
?>

<div id="arti-sec<?php echo $section->secOrder;?>" class="sectionContent">
<h2>[<span class="sectionContent-title" secID="<?php echo $section->secOrder;?>"><?php echo $section->secName;?></span>]

<?php
	//該章節有資料夾，建立資料夾檢視連結
	if (is_dir($filePath.$section->secName)):
?>
<a class="sectionContent-EditDir" target="_blank" href="editDirectory.php?site=<?php echo $sitePath;?>&path=<?php echo urlencode($section->secName);?>"><i class="icon-folder"></i></a>

<?php
	//該章節沒有資料夾，建立新增按鈕
	else:
?>
<span class="sectionContent-addDir" secID="<?php echo $section->secOrder;?>" secName="<?php echo $section->secName;?>"><i class="icon-plus"></i> 建立章節資料夾</span>
<?php
	endif;
?>

</h2>
<textarea id="sec<?php echo $section->secOrder;?>" name="sec<?php echo $section->secOrder;?>" class="mceEditor">
<?php echo $section->content;?>
</textarea>
</div>

<?php
	}
?>



</div>
<div id="searchPanelArea">
<div class="panelHeader">
<h3>搜尋工具</h3>
<form id="searchForm">
<input id="searchFormInput" placeholder="輸入要搜尋的詞彙..."></input>
<input type="submit"></input>
</form>
<ul id="searchTypeBtns">
	<li><a href="#sr_website" class="active">網頁</a></li>
	<li><a href="#sr_image">圖片</a></li>
	<li><a href="#sr_news">新聞</a></li>
	<li><a href="#sr_nupedia">NuPedia</a></li>
</ul>
</div>
<div id="searchResultArea">
	<div id="sr_website" class="searchResultContent">
		<ul>
			<!--li>
				<a href="#">莫札特 霍夫曼</a>
				<p>這邊是備註，這邊是備註，這邊是備註，這邊是備註，這邊是備註</p>
			</li-->

		</ul>	
	</div>
	<div id="sr_image" class="searchResultContent Collage">


	</div>
	<div id="sr_news" class="searchResultContent">
		<ul>
			<!--li>
				<a href="#">莫札特 霍夫曼</a>
				<p>這邊是備註，這邊是備註，這邊是備註，這邊是備註，這邊是備註</p>
			</li-->

		</ul>	
	</div>
	<div id="sr_nupedia" class="searchResultContent searchResultContentPic">
		<ul>
		</ul>
	</div>
</div>
</div>
</div>

<script>
$(document).ready( function() {

	defaultSecId = <?php echo $selectedSecID;?>;

	tinyMCEInit();
	
	sectionListCount = refreshSectionList();
	
	sectionContentList.each( function(a, b){
		b.hide();
	});	
	
	if(defaultSecId<sectionListCount){
		sectionTabList.filter("[href=#arti-sec"+defaultSecId+"]").addClass("active");
		sectionContentList[defaultSecId].show();
	}
	else{
		sectionTabList.filter("[href=#arti-sec0]").addClass("active");
		sectionContentList[0].show();	
	}
	
	
	$(function() {
		$( "#sectionTabs" ).sortable({
			items: "li:not(.disabledDrag)",
			distance: 15,
			placeholder: "placeholder",
			update: sectionTabsOrderChange,
		});
		//$( "#edit-sectionTabList" ).disableSelection();
	});
	
	$('#addSectionBtn').click(function(e){
		$('#sectionTabs').append('<li><a href="#arti-sec'+(sectionListCount+1)+'">new section</a></li>');
	
		$('#contentArea').append('<div id="arti-sec'+(sectionListCount+1)+'" class="sectionContent"><h2>[<span class="sectionContent-title" secID="'+(sectionListCount+1)+'">new section</span>]</h2><textarea id="sec'+(sectionListCount+1)+
		'" name="sec'+(sectionListCount+1)+'" class="mceEditor"></textarea></div>');

		tinyMCE.execCommand('mceAddControl', false, 'sec'+(sectionListCount+1));	
		sectionListCount=refreshSectionList();

	});
	
	
	
	$('#saveBtn').click(function(e){
		e.preventDefault();
		
		$("#saveStatus").html("變更儲存中...");
		$("#saveBtn").prop('disabled', true);
		$("#exitBtn").prop('disabled', true);

		 
		tinyMCE.triggerSave();
		
		var wrapContent = wrapEditContent();		
		
		var request = $.ajax({
		  url: "edit_process.php?site=<?php echo $sitePath;?>",
		  type: "POST",
		  data: { editby: '<? echo $USER_ID;?>', content : wrapContent },
		  dataType: "json"
		});
		 
		request.done(function( jData ) {

			if(jData.status=='ok'){
				$("#saveStatus").html("最後編輯時間:"+jData.lastEditDate);
				$("#saveBtn").prop('disabled', false);
				$("#exitBtn").prop('disabled', false);
			}
			else{
				$("#saveStatus").html("儲存錯誤，請重試");
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
		window.location = 'index.php?site=<?php echo $sitePath;?>';
	
	});

	//新增條目附加資料夾
	$(".sectionContent-addDir").click(function(e){

		var folderName = $(this).attr("secName");
		var secID = $(this).attr("secID");
		
		
		var isConfirm = confirm("確定要為章節["+folderName+"]新增一個檔案資料夾?");
		if (isConfirm) {

		
	
		var request = $.ajax({
		  url: "edit_createSubFolder_process.php?site=<?php echo $sitePath;?>",
		  type: "POST",
		  data: { eid: '<?php echo $entryID;?>', secID: secID, folderName: folderName },
		  dataType: "json"
		});
		 
		request.done(function( jData ) {

			if(jData.status=='ok'){
			
				var secContentHeader = $("#arti-sec"+jData.secID);
				
				$("#arti-sec"+jData.secID+" h2 .sectionContent-addDir").remove();
				$("#arti-sec"+jData.secID+" h2").append('<a class="sectionContent-EditDir" target="_blank" href="editDirectory.php?site='+jData.sitePath+'&path='+jData.folderName+'"><i class="icon-folder"></i></a>');
				
				//$(this).remove();
			
			}
			else{

			}
		});
		 
		request.fail(function( jqXHR, textStatus ) {
		  alert( "Request failed: " + textStatus );
		});
	
		}
	});
	

	$( "#searchForm" ).submit(function( e ) {
	    e.preventDefault();
		
		var queryStr = $("#searchFormInput").val();

		getSearchResult_website(queryStr);
		getSearchResult_news(queryStr);
		getSearchResult_image(queryStr);
		getSearchResult_nupedia(queryStr);
		
		return false;
	});

	searchTypeBtns = $("#searchTypeBtns a");

	srContents = $("#searchResultArea .searchResultContent")
	sr_image_width = $("#sr_image").width();
	srContents.hide();
	$("#sr_website").show();

	searchTypeBtns.click(function(e){
		e.preventDefault();

		seletedSrType = $(this).attr("href");

		searchTypeBtns.removeClass("active").filter("[href="+seletedSrType+"]").addClass("active");
		srContents.hide();

		$(seletedSrType).show();


	})
	



});

function imageCollage(albumWidth){
$('.Collage').removeWhitespace().collagePlus(
        {

            'targetHeight'    : 160,
            'albumWidth'	  : albumWidth-17,
            'fadeSpeed'       : "fast",

            /*
             * which effect you want to use for revealing the images (note CSS3 browsers only),
             * Options are effect-1 to effect-6 but you can also code your own
             * Default is the safest option for supporting older browsers
             */
            'effect'          : 'default',

        }
);
}

function sectionTabsOrderChange(){

	sectionTabList = $('#sectionTabs a');

}

function refreshSectionList(){
	sectionTabList = $('#sectionTabs a');
	
	
	sectionContentList = sectionTabList.map(function(){
		var item = $($(this).attr("href"));
	
		if(item.length){
			return item;
		}
	});
	
	sectionTabList.click( function(e){
		var href = $(this).attr("href");
		
		sectionContentList.each( function(a, b){
			b.hide();
		});
		
		sectionTabList.removeClass("active");
		
		$(href).show();
		sectionTabList.filter("[href="+href+"]").addClass("active");
		e.preventDefault();
	});
	
	$('.sectionContent span.sectionContent-title').dblclick(function() {
	
		if($(this).has('form').length==0){
		
		var selectedSecID = $(this).attr("secID");
	
		$(this).html("<form class=\"sectionContent-editForm\" secID="+selectedSecID+"><input id=\"sectionContent-editTitle\" type=\"text\" value=\""+$(this).html()+"\"></input><input type=\"submit\"></input></form>");
	
		$('#sectionContent-editTitle').focus();
		$('form.sectionContent-editForm').submit(function(e){
			e.preventDefault();

			var newTitle = $('#sectionContent-editTitle').val();
			
			var parentSpan = $(this).parent();
			$(this).remove();
			parentSpan.html(newTitle);
			
			sectionTabList.filter("[href=#arti-sec"+selectedSecID+"]").html(newTitle);
			
		});
		
		}
	
	});


	return sectionTabList.length;
}



function wrapEditContent(){

	var secCt=0;
	var editObjs = sectionTabList.map(function(){
		
		var tab = $(this);
		var secID = tab.attr("href");
		var sectionObj = {};
				console.log( tab);
		sectionObj['secOrder']=secCt;
		sectionObj['title']=tab.text();
		sectionObj['content']= $(secID+" textarea").val();

		secCt++;
	
		return sectionObj;
	});

	var editContent = []
	
	for(var i=0;i<editObjs.length;i++)
		editContent.push(editObjs[i]);
		
	return editContent;
}

function getCurDtStr(){
	var curDt = new Date();
	var monthStr = curDt.getMonth()+1;
	var dayStr = curDt.getDate();
	var hourStr = curDt.getHours();
	var minStr = curDt.getMinutes();
	
	var dtStr="";
	dtStr+=curDt.getFullYear();
	dtStr+="/"+((monthStr<10)? "0"+monthStr:monthStr);
	dtStr+="/"+((dayStr<10)? "0"+dayStr:dayStr);
	dtStr+=" "+((hourStr<10)? "0"+hourStr:hourStr);
	dtStr+=":"+((minStr<10)? "0"+minStr:minStr);
	
	return dtStr;
}

function tinyMCEInit(){

tinyMCE.init({
	mode:"textareas",
	theme:"advanced",
	editor_selector:"mceEditor",
	language:"zh-tw",
<!-- 外掛功能：有 ooki_ 開頭的是 ooki 加入的功能 -->
	plugins:"ooki_annotation, ooki_youtube, ooki_pushpage, ooki_cleartag, ooki_usercss, ooki_meta, ooki_tempcontent, ooki_abstract, ooki_bgsound, ooki_imgresize, ooki_questresult, ooki_quest, ooki_physicallink, ooki_imgbox, ooki_totable, ooki_imageleft, ooki_hidecontent, ooki_internalsearch, ooki_create_article, ooki_webcam, media, style, table, advimage, advlink, emotions,iespell,inlinepopups, preview, contextmenu, paste, directionality, fullscreen, noneditable, visualchars, nonbreaking, xhtmlxtras, wordcount, advlist, searchreplace",
	content_css:"css/nupedia.css",
	width:"100%",
	height:"500px",
	media_use_script:true,
	theme_advanced_fonts:"\u65b0\u7d30\u660e\u9ad4=\u65b0\u7d30\u660e\u9ad4,\u7d30\u660e\u9ad4;\u6a19\u6977\u9ad4=\u6a19\u6977\u9ad4,Andale Mono=andale mono,times;Arial=arial,helvetica,sans-serif;Arial Black=arial black,avant garde;Book Antiqua=book antiqua,palatino;Comic Sans MS=comic sans ms,sand;Courier New=courier new,courier;Georgia=georgia,palatino;Helvetica=helvetica;Impact=impact,chicago;Symbol=symbol;Tahoma=tahoma,arial,helvetica,sans-serif;Terminal=terminal,monaco;Times New Roman=times new roman,times;Trebuchet MS=trebuchet ms,geneva;Verdana=verdana,geneva;Webdings=webdings;Wingdings=wingdings,zapf dingbats",
	theme_advanced_toolbar_location:"top",
	theme_advanced_toolbar_align:"left",
	theme_advanced_buttons1:"usercss,bold,italic,underline,strikethrough,|,forecolor,backcolor,|,cut,copy,paste,cleanup,|,search,replace,|,undo,redo,|,fontselect,fontsizeselect,|,code,", 
	theme_advanced_buttons2:"justifyleft,justifycenter,justifyright,justifyfull,|,outdent,indent,|,bullist,numlist,|,sub,sup,|,hr,charmap,|,visualaid,|,tablecontrols", 
<!-- ooki 的功能按鈕有：createdir,createpage,selecttopage,rs_dirtable,internalsearch,annotation,imageleft,totable,imgbox,physicallink,quest,questresult,hidecontent,|,imgresize,audio,video,youtube,pushpage,clearall,cleartag,bgsound,tempcontent,abstract,lastcontent,meta -->
	theme_advanced_buttons3:"image,link,unlink,flash,cleanup,|,createdir,createpage,selecttopage,rs_dirtable,internalsearch,annotation,imageleft,totable,imgbox,physicallink,quest,questresult,hidecontent,|,imgresize,media,audio,video,youtube,pushpage,|,clearall,cleartag,bgsound,tempcontent,abstract,lastcontent,fullscreen,meta", 
	theme_advanced_statusbar_location:"bottom",
	theme_advanced_resizing:true,
	theme_advanced_resize_horizontal:false,
	theme_advanced_resizing_use_cookie:false,
	extended_valid_elements:"script[src|language]," + 
							"style[id]," +
							"iframe[src|frameborder|width|height]," +
							"+a[id|style|rel|rev|charset|hreflang|dir|lang|tabindex|accesskey|type|name|href|target|title|class|onfocus|onblur|onclick|annotationdata|ondblclick|onmousedown|onmouseup|onmouseover|onmousemove|onmouseout|onkeypress|onkeydown|onkeyup]," + 
							"div[id|name|dir|class|align|style|title|onclick|data]," +
							"span[style|class|align|id|name|title|onclick|annotationdata|onmouseover|onmouseout|data]," +
							"embed[src|width|height|type|wmode]," +
							"img[data|id|name|width|height|class|align|title|src|arg|onload|border|summary|bgcolor|background|bordercolor|style]," +
							"table[width|height|id|name|class|style|title|border|cellspacing|cellpadding]," +
							"tr[id|name|width|height|style|bgcolor], td[id|name|width|height|style|bgcolor]," +
							"input[id|name|class|type|size|title|value|checked|disabled]," +
							"select[id|name|class|size|value], option[id|name|value]," +
							"p[id|name|style]," +
							"center",
	convert_urls : false,
	convert_fonts_to_spans : true,
	paste_auto_cleanup_on_paste	: false,
	custom_undo_redo_levels : 10,
	setup : ooki_MCE_Setup
});
}

function getSearchResult_website(queryStr){

	var request = $.ajax({
	  url: "search_func.php?type=w&query="+encodeURIComponent(queryStr),
	  type: "GET",
	  dataType: "json"
	});
	 
	request.done(function( jData ) {
		console.log(jData);

		var resultList = [];
		for(var i=0;i<jData.length;i++){
			resultList.push("<li><a class=\"sr-title\" href=\""+jData[i].link+"\">"+jData[i].title+"</a><p class=\"sr-desc\">"+jData[i].desc+"</p></li>");
		}

		$("#sr_website ul").html(resultList);
	});

	 
	request.fail(function( jqXHR, textStatus ) {
	  alert( "Request failed: " + textStatus );
	});


}

function getSearchResult_news(queryStr){

	var request = $.ajax({
	  url: "search_func.php?type=n&query="+encodeURIComponent(queryStr),
	  type: "GET",
	  dataType: "json"
	});
	 
	request.done(function( jData ) {
		console.log(jData);

		var resultList = [];
		for(var i=0;i<jData.length;i++){
			resultList.push("<li><a class=\"sr-title\" href=\""+jData[i].link+"\">"+jData[i].title+"</a><span class=\"sr-src\">"+jData[i].src+"</span><p class=\"sr-desc\">"+jData[i].desc+"</p></li>");
		}

		$("#sr_news ul").html(resultList);
	});

	 
	request.fail(function( jqXHR, textStatus ) {
	  alert( "Request failed: " + textStatus );
	});


}

function getSearchResult_image(queryStr){

	var request = $.ajax({
	  url: "search_func.php?type=i&query="+encodeURIComponent(queryStr),
	  type: "GET",
	  dataType: "json"
	});
	 
	request.done(function( jData ) {
		console.log(jData);

		var resultList = [];
		for(var i=0;i<jData.length;i++){
			resultList.push("<img src=\""+jData[i].link+"\" width=\""+jData[i].width+"\" height=\""+jData[i].height+"\">");
		}

		$("#sr_image").html(resultList);
		imageCollage(sr_image_width);
	});

	 
	request.fail(function( jqXHR, textStatus ) {
	  alert( "Request failed: " + textStatus );
	});
	
}

function getSearchResult_nupedia(queryStr){

	var request = $.ajax({
	  url: "search_func.php?type=p&query="+encodeURIComponent(queryStr),
	  type: "GET",
	  dataType: "json"
	});
	 
	request.done(function( jData ) {
		console.log(jData);

		var resultList = [];
		for(var i=0;i<jData.length;i++){
			resultList.push("<li><div><img class=\"sr-img\" style=\"background-image: url('"+jData[i].image+"');\"/><div class=\"sr-header\"><a class=\"sr-title\" href=\""+jData[i].link+"\">"+jData[i].title+"</a><span class=\"sr-src\">作者 "+jData[i].author+"</span></div></div><p class=\"sr-desc\">"+jData[i].abstract+"</p></li>");
		}

		$("#sr_nupedia ul").html(resultList);
	});

	 
	request.fail(function( jqXHR, textStatus ) {
	  alert( "Request failed: " + textStatus );
	});


}
/*
function BnSave() 
{
	// ooki_meta_getContent: 取得 meta 資料
	// ooki_getContent: 取得 編輯內容.
	var sC = ooki_meta_getContent() + ooki_getContent("txtContent");
	alert(sC);
}
*/
</script>

</body>

</html>