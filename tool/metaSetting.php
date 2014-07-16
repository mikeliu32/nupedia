<?php
include_once('inc/auth.php');
require_once('pathManage.php');

if($IS_ENTRY_EXIST){
	$metafile = $dataHome.$sitePath."/metainfo.json";
	$metainfo = json_decode(file_get_contents($metafile));

	$IS_AUTHOR = isAuthor($metainfo->author);
	
	if(!$IS_AUTHOR){
		header("Location: error.php");
		die();
	}
	
	$meta = $metainfo->meta;

	$contentfile = $dataHome.$sitePath."/content.json";
	$article = json_decode(file_get_contents($contentfile));


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
<link rel="stylesheet" href="css/metaSetting.css" type="text/css">
<link rel="stylesheet" href="css/fonts.css" type="text/css">
<link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" type="text/css">
<!--script src="http://code.jquery.com/jquery-1.10.2.min.js"></script-->
<script src="js/jquery-1.11.0.min.js"></script>
<script src="js/jquery-ui-1.10.4.custom.min.js"></script>

<script src="js/jquery.collagePlus.min.js"></script>
<script src="js/jquery.removeWhitespace.min.js"></script>
<!-- load jquery form plugin for image ajax upload -->
<script src="js/jquery.form.min.js"></script>


<title>NuPedia-Edit</title>
</head>

<body>
<div id="edit-Wrapper">
<div id="leftPanelArea">
<div id="leftPanel">
<div class="panelHeader">
<div class="metaSettingTitle">
<h3>條目設定</h3>
<div id="deleteEntryBtn"><i class="icon-remove"></i> <span>刪除條目</span></div>
</div>
<div class="panelHeader-title">條目名稱</div>
<p id="entryinfo-title"><?php echo $metainfo->title;?></p>
<div class="panelHeader-title">作者</div>
<p><?php echo $metainfo->author;?></p>
</div>

<div class="panelHeader-title">協作者
<a href="#" id="addCollabBtn" class="panelHeader-title-right">+新增</a>
</div>
<div id="collabWrap" class="panelTagsWrap">
<?php
	if($metainfo->collaborator){
		foreach($metainfo->collaborator as $collab)
			echo "<span>$collab</span>";
	}
?>
</div>

<div class="panelHeader-title">設定</div>
<div class="setting-controls">
	<span class="setting-label">公開知識檔案</span>
	<div class="setting-toggleSwitch">
		<input id="isEntryVisible-toggle" class="toggleSwitch" type="checkbox" <?php echo $metainfo->isVisible? "checked":"";?> >
		<label for="isEntryVisible-toggle"></label>
	</div>
</div>
<div class="setting-controls">
	<span class="setting-label">開放分支版本</span>
	<div class="setting-toggleSwitch">
		<input id="isForkable-toggle" class="toggleSwitch" type="checkbox" <?php echo $metainfo->isForkable? "checked":"";?> >
		<label for="isForkable-toggle"></label>
	</div>
</div>

<div class="panelHeader-title">標籤
<a href="#" id="addTagsBtn" class="panelHeader-title-right">+新增</a>
</div>
<div id="tagsWrap" class="panelTagsWrap">
<?php
	if($metainfo->tag){
	foreach($metainfo->tag as $tag)
		echo "<span class=\"lightcolor\">$tag</span>";
	}
?>
</div>


</div>
<div id="leftPanelBtns">
	<span id="saveStatus" class="hint-invert asBlock"></span>
	<a href="#" id="saveBtn" class="leftPanelBtn save">儲存</a>
	<a href="#" id="exitBtn" class="leftPanelBtn cancel">離開</a>
</div>
</div>
<div id="contentArea">
<h2>條目資訊編輯</h2>
<div id="metaEditArea">
<div id="editImage">
	<div id="origin-img">
		<img src="<?php echo $metainfo->image? $dataHome.$sitePath."/images/".$metainfo->image : "defaultPic.png"?>">
		<span id="uploadImageStatus">上傳中...</span>
	</div>
	<form id="uploadImageForm">
	  <input id="uploadImage" type="file" accept="image/*" name="image" />
	  <input id="uploadImageBtn" type="submit" value="更改圖片">
	</form>
</div>
<div id="editMeta">
	<form id="updateMetaForm">

		<label for="title">條目名字/名稱</label>
		<input id="title" type="text" value="<?php echo $metainfo->title;?>"></input>

		<label for="etitle">條目名字/名稱(英文)</label>
		<input id="etitle" type="text" value="<?php echo $metainfo->etitle;?>"></input>
		
		<div id="colHeader">
		<h3>資訊欄位</h3>
		<span id="addColBtn"><i class="icon-plus"></i> 新增</span>
		</div>
<?php
		if($meta){
		for($i=0;$i<count($meta);$i++){
			$colName = "meta-".$i;
?>
			<div class="col-group" metaname="<?php echo $meta[$i]->name;?>">
			<span class="metaCol-panel"><i class="metaCol-editBtn icon-pencil"></i><i class="metaCol-removeBtn icon-remove"></i><i class="metaCol-upBtn icon-angleb-up"></i><i class="metaCol-downBtn icon-angleb-down"></i></span>
			<label for="<?php echo $colName; ?>"><?php echo $meta[$i]->name;?></label>
			<input id="<?php echo $colName; ?>" type="text" value="<?php echo $meta[$i]->value;?>" metaname="<?php echo $meta[$i]->name;?>"></input>
			</div>
<?php
		}
		}
?>

	</form>
</div>

</div>
</div>

</div>
<div id="promptDialog" class="dialogWrap">
</div>
<div id="removingDialog" class="dialogWrap">
<div class="dialog"><h3>刪除條目中，請稍後...</h3><p>過程中請勿重新整理，刪除完成後頁面將會自動跳轉</p></div>
</div>
<script>
$(document).ready( function() {

	
	
	
	$('#deleteEntryBtn').click(function(e){
		var isDelete = confirm("條目一經刪除，其所有的資料與檔案皆會被移除。確認要刪除條目?");
		if (isDelete) {
			$("#removingDialog").show();
			removeEntry();
		}
	});
	
	$('#addCollabBtn').click(function(e){
		$('#promptDialog').html('<div id="dialog_addCollab" class="dialog"><h3>新增協作者</h3><input type="text"></input><button>新增</button><span class="closeDialogBtn">X</span></div>');
		
		$('.dialog .status').hide();
		$('#promptDialog').show();
	
		$('#promptDialog .closeDialogBtn').click(function(e){
			$('#promptDialog').hide();
		});

		$('.dialog button').click(function(e){
			
			var newCollab = $('.dialog input').val();
			if(newCollab.length>0){
				var newSpan = $("<span>"+newCollab+"</span>");
				$('#collabWrap').append(newSpan);
				$('#promptDialog').hide();
			}
			
			$(newSpan).click(function(e){
				var isDelete = confirm("確認要刪除協作者 ["+$(this).html()+" ]?");
				if (isDelete) {
				$(this).remove();
			}
			});
			
		});
		
		e.preventDefault();

	});
	
	$('#addTagsBtn').click(function(e){
		$('#promptDialog').html('<div id="dialog_addTags" class="dialog"><h3>新增標籤</h3><input type="text"></input><button>新增</button><span class="closeDialogBtn">X</span></div>');
		
		$('.dialog .status').hide();
		$('#promptDialog').show();
	
		$('#promptDialog .closeDialogBtn').click(function(e){
			$('#promptDialog').hide();
		});
		
		$('.dialog button').click(function(e){
			
			var newTag = $('.dialog input').val();
			if(newTag.length>0){
				var newSpan = $("<span class=\"lightcolor\">"+newTag+"</span>");

				$('#tagsWrap').append(newSpan);
				$('#promptDialog').hide();
			}
			
			$(newSpan).click(function(e){
				var isDelete = confirm("確認要刪除標籤 ["+$(this).html()+" ]?");
				if (isDelete) {
					$(this).remove();
				}
			});
			
		});
		
		e.preventDefault();

	});	

	$('#collabWrap span').click(function(e){
		var isDelete = confirm("確認要刪除協作者 ["+$(this).html()+" ]?");
		if (isDelete) {
			$(this).remove();
		}
	});
	
	$('#tagsWrap span').click(function(e){
		var isDelete = confirm("確認要刪除標籤 ["+$(this).html()+" ]?");
		if (isDelete) {
			$(this).remove();
		}
	});
	
	
	$('#saveBtn').click(function(e){
		e.preventDefault();
		
		$("#saveStatus").html("變更儲存中...");
		$("#saveBtn").prop('disabled', true);
		$("#exitBtn").prop('disabled', true);

		 
		var metaCols = getMetaContent();
		var colTitle = $("#title").val();
		var colETitle = $("#etitle").val();
		
		var visible = $("#isEntryVisible-toggle").prop("checked")? 1 : 0;
		var forkable = $("#isForkable-toggle").prop("checked")? 1 : 0;
		
		var collabs = $('#collabWrap span').map(function(){ return this.innerHTML;});
		var tags = $('#tagsWrap span').map(function(){ return this.innerHTML;});

		var request = $.ajax({
		  url: "metaSetting_process.php?site=<?php echo $sitePath;?>&type=m",
		  type: "POST",
		  data: { title: colTitle, etitle: colETitle , isVisible: visible, isForkable: forkable, collaborator: collabs.toArray(), tag: tags.toArray(), meta: metaCols },
		  dataType: "json"
		});
		 
		request.done(function( jData ) {

			if(jData.status=='ok'){
				$("#entryinfo-title").html(colTitle);

				$("#saveStatus").html("最後編輯時間:"+getCurDtStr());
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
	
	
	$('#uploadImageForm').submit(function(e){
		e.preventDefault();

		$(this).ajaxSubmit({
		  url:	'metaSetting_process?site=<?php echo $sitePath;?>&type=i', 
		  dataType: 'json',
		  type: 'post',
		  beforeSend: function(){
				$("#uploadImageBtn").prop('disabled',true);
				$("#saveBtn").prop('disabled', true);
				$("#exitBtn").prop('disabled', true);
				
				$("#uploadImageStatus").html("上傳中...");
				$("#uploadImageStatus").show();
		  },
		  success: function(jData) {
			console.log(jData);
		
			if(jData.status=='ok'){
				var imgPath = "<?php echo $dataHome.$sitePath."/images/";?>";
				$("#origin-img img").attr("src", imgPath+jData.image);
				$("#uploadImageStatus").html("上傳成功!");
				$("#uploadImageStatus").delay(3000).fadeOut();
			}
			else{
				$("#uploadImageStatus").html("上傳失敗:"+jData.msg);
				$("#uploadImageStatus").delay(3000).fadeOut();
			}
			$(this).resetForm();
			$("#uploadImageBtn").prop('disabled',false);
			$("#saveBtn").prop('disabled', false);
			$("#exitBtn").prop('disabled', false);
		},
		  error: function(e){
		  
			$("#uploadImageStatus").html("上傳失敗:(");
			$("#uploadImageStatus").delay(3000).fadeOut();
		  
			$("#uploadImageBtn").prop('disabled',false);
			$("#saveBtn").prop('disabled', false);
			$("#exitBtn").prop('disabled', false);
		  }
		});
		
	});

	$("#addColBtn").click(function(e){
		e.preventDefault();
		
		var newColName = prompt("新增欄位名稱");

		if (newColName!=null) {
			var metaform = $("#updateMetaForm");
		
			var curColCt = $(".col-group").length;
			var newColId = "meta-"+(curColCt+1);
			
			var $newColContent = $("<div class=\"col-group\">");
			$newColContent.attr("metaname", newColName);
			$newColContent.append("<span class=\"metaCol-panel\"><i class=\"metaCol-editBtn icon-pencil\"></i><i class=\"metaCol-removeBtn icon-remove\"></i><i class=\"metaCol-upBtn icon-angleb-up\"></i><i class=\"metaCol-downBtn icon-angleb-down\"></i></span>");
			
			var $newColLabel = $("<label></label>");
			$newColLabel.attr("for",newColId);
			$newColLabel.html(newColName);
			
			var $newColInput = $("<input type=\"text\"></input>");
			$newColInput.attr("id",newColId);
			$newColInput.attr("metaname",newColName);
			
			$newColContent.append($newColLabel);
			$newColContent.append($newColInput);
		
			metaform.append($newColContent);
		}
	});
	
	$("#updateMetaForm").on("click", ".metaCol-editBtn" ,(function() {
		

		var colDiv = $(this).parents(".col-group");
		
		var label =  colDiv.find("label");
		
		var newColname = prompt("修改欄位名稱", label.html());

		if (newColname!=null) {
			label.html(newColname);
			colDiv.find("input").attr("metaname", newColname);
		}
		
		
	}));

	$("#updateMetaForm").on("click", ".metaCol-removeBtn",(function() {
		
		var colDiv = $(this).parents(".col-group");
		var labelName =  colDiv.find("label").html();

		var isRemove=confirm("確定要刪除欄位["+labelName+"]?");
		if(isRemove==true)
			colDiv.remove();
		
	}));
	
	$("#updateMetaForm").on("click", ".metaCol-upBtn,.metaCol-downBtn" ,(function() {
		
		
		var curColDiv = $(this).parents(".col-group");
		
		if($(this).is('.metaCol-upBtn')){
			
			curColDiv.insertBefore(curColDiv.prev(".col-group"));
		}
		else{
			curColDiv.insertAfter(curColDiv.next(".col-group"));
		}
		
		
	}));


});


function getMetaContent(){

	var cols = $(".col-group input");
	
	var colObjs = cols.map(function(){
		
		var col = $(this);
		
		var colObj = {};
		colObj['name']=col.attr("metaname");
		colObj['value']=col.val();
	
		return colObj;
	});
	
	var metaResult = [];	

	for(var i=0;i<colObjs.length;i++)
		metaResult.push(colObjs[i]);
		
	return metaResult;
	
}

function removeEntry(){

var request = $.ajax({
	  url: "deleteEntry_process.php?site=<?php echo $sitePath;?>",
	  type: "POST",
	  data: { eid: '<?php echo $entryID;?>' },
	  dataType: "json"
	});
	 
	request.done(function( jData ) {

		if(jData.status=='ok'){
		
			$("#removingDialog .dialog").html("<h3>刪除成功! 跳轉中...</h3>");
			
			 window.setTimeout(function(){
				window.location = 'user.php';
			}, 3000);
		
		}
		else{
			$("#removingDialog .dialog").html("<h3>刪除失敗! 請重試</h3>");

		}
	});
	 
	request.fail(function( jqXHR, textStatus ) {
	  alert( "Request failed: " + textStatus );
	});

}




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

</script>

</body>

</html>