<?php
require_once('pathManage.php');

date_default_timezone_set('Asia/Taipei');

$metafile = $dataHome.$sitePath."/metainfo.json";
$metainfo = json_decode(file_get_contents($metafile));

$meta = $metainfo->meta;

$rootPath = $dataHome.$sitePath."/files";

if(isset($_GET['path'])){

	$curPath = $_GET['path'];

	if(count($curPath)==0){
		$comPath = $rootPath;
	}
	else{

		$comPath = $rootPath."/".$curPath;
		$breadCrumb = split("/", $curPath);
	}

}
else
	$comPath = $rootPath;

$files = scandir($comPath);

$fileIconSet = ["dir"=>"icon-folder","png"=>"icon-file-image","jpg"=>"icon-file-image","gif"=>"icon-file-image",
				"jpeg"=>"icon-file-image","doc"=>"icon-file-word","docx"=>"icon-file-word",
				"xls"=>"icon-file-excel","xlsx"=>"icon-file-excel","ppt"=>"icon-file-powerpoint",
				"pptx"=>"icon-file-powerpoint","pdf"=>"icon-file-pdf"];

$file_list=array();

for($i=2;$i<count($files);$i++){

	$fileinfo = pathinfo($files[$i]);

	$tmpfile = array();

	$tmpfile['name'] = $fileinfo['filename'];
	$tmpfile['basename'] = $fileinfo['basename'];
	$tmpfile['path'] = ($curPath)? $curPath."/".$fileinfo['basename']: $fileinfo['basename'];

	if(($fileExt = $fileinfo['extension'])==NULL)
		$tmpfile['ext'] = "dir"; 
	else
		$tmpfile['ext'] = $fileExt;

	if(($fileIcon = $fileIconSet[strtolower($tmpfile['ext'])]))
		$tmpfile['icon'] = $fileIcon;
	else
		$tmpfile['icon'] = "icon-file";

	$tmpfile['mtime'] = date("Y/m/d H:i:s", filemtime($comPath."/".$tmpfile['basename']));
	$file_list[] = $tmpfile;
}

//sorting

foreach($file_list as $key=>$row){
	$f_name[$key] = $row['name'];
	$f_ext[$key] = $row['ext'];
}
if(count($file_list)>0)
	array_multisort($f_ext, SORT_ASC, $f_name, SORT_ASC, $file_list);

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="css/normalize.css" type="text/css">
<link rel="stylesheet" href="css/editDir.css" type="text/css">
<link rel="stylesheet" href="css/fonts.css" type="text/css">
<link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" type="text/css">
<!--script src="http://code.jquery.com/jquery-1.10.2.min.js"></script-->
<script src="js/jquery-1.11.0.min.js"></script>
<script src="js/jquery-ui-1.10.4.custom.min.js"></script>

<title>NuPedia-EditDirectory</title>
</head>

<body>
<div id="edit-Wrapper">
<div id="leftPanelArea">
<div id="leftPanel">
<div class="panelHeader">
<h3>編輯資料夾</h3>
<div class="panelHeader-title">條目名稱</div>
<p><?php echo $metainfo->title;?></p>
<div class="panelHeader-title">作者</div>
<p><?php echo $metainfo->author;?></p>
<div class="panelHeader-title">檔案資訊
<span class="hint-invert asBlock">點選右側檔案以瀏覽資訊</span>
</div>
</div>

<ul id="sectionTabs">

<!--li><a href="#arti-sec2">作品</a></li-->
</ul>
</div>
<div id="leftPanelBtns">
	<span id="saveStatus" class="hint-invert asBlock"></span>
	<a href="#" id="exitBtn" class="leftPanelBtn cancel">離開</a>
</div>
</div>
<div id="contentArea">
<div id="dirViewer-header">

<ul id="dirBreadcrumb">
<li><a href="editDirectory.php?site=<?php echo $sitePath;?>"><?php echo $metainfo->title;?></a></li>

<?php
	if($breadCrumb){
	foreach($breadCrumb as $key=>$dirName){

		$breadPath = "";
		for($i=0;$i<=$key;$i++){

			if($i!=0)
				$breadPath.="/";

			$breadPath.=$breadCrumb[$i];

		}
			
		echo "<li><a href=\"editDirectory.php?site=".$sitePath."&path=".urlencode($breadPath)."\">$dirName</a></li>";
	}
	}
?>


</ul>

<div id="dirViewerControl">
</div>

<div id="dirViewerListHeader">
<span class="viewerListCol-icon"></span><span class="viewerListCol-name">名稱</span><span class="viewerListCol-type">類型</span><span class="viewerListCol-updateTime">更新時間</span>
</div>

</div>
<div id="dirViewer-content">

<ul id="dirViewerList">

<?php

	for($i=0; $i<count($file_list); $i++){
		$file= $file_list[$i];
?>
	<li><span class="viewerListCol-icon"><i class="<?php echo $file['icon'];?>"></i></span>
		<span class="viewerListCol-name"><a href="editDirectory.php?site=<?php echo $sitePath;?>&path=<?php echo urlencode($file['path']);?>"><?php echo $file['name'];?></a></span>
		<span class="viewerListCol-type"><?php echo $file['ext'];?></span>
		<span class="viewerListCol-updateTime"><?php echo $file['mtime'];?></span></li>

<?php
	}
?>

</ul>

</div>

<div id="fUploadStatusList">
	<ul>

	</ul>
</div>

<div id="dragFileWindow">
<span>上傳檔案到此資料夾</span>
</div>
</div>


</div>
</div>

<script>
$(document).ready( function() {

		
	
	
	var drapFileArea = $("body");
	var dragging =0;
	drapFileArea.on('dragenter', function (e) 
	{
		dragging++;
		e.stopPropagation();
		e.preventDefault();
		$("#dragFileWindow").show();
	});
	
	drapFileArea.on('dragover', function (e)
	{
		 e.stopPropagation();
		 e.preventDefault();
	});

	drapFileArea.on('dragleave', function (e)
	{
		dragging--;
		
		 e.stopPropagation();
		 e.preventDefault();
		
		if(dragging===0)
			$("#dragFileWindow").hide();
	});
	
	drapFileArea.on('drop', function (e)
	{
		 e.preventDefault();
		 var files = e.originalEvent.dataTransfer.files;
		console.log(files);
		 //We need to send dropped files to Server
		handleFileUpload(files);
		$("#dragFileWindow").hide();
	});
	
	
	$('#saveBtn').click(function(e){
		e.preventDefault();
		

	
	});
	

	$('#exitBtn').click(function(e){
		e.preventDefault();
		window.location = 'index.php?site=<?php echo $sitePath;?>'
	
	});
	



});


function handleFileUpload(files)
{

   var uploadpath ="<?php echo $comPath;?>";

   for (var i = 0; i < files.length; i++) 
   {
        var fd = new FormData();
        fd.append('file', files[i]);
		fd.append('uploadpath',uploadpath);
 
		 $("#fUploadStatusList").show();
        var fUploadStatus = new createFUploadStatusbar($("#fUploadStatusList ul"));
		 //Using this we can set progress.
		fUploadStatus.setFileName(files[i].name);
       
	   	sendFileToServer(fd, fUploadStatus);
 
   }
}


function sendFileToServer(formData, fUploadStatusbar)
{
    var uploadURL ="fileUpload_process.php?site=<?php echo $sitePath;?>"; //Upload URL

    var jqXHR=$.ajax({
            xhr: function() {
            var xhrobj = $.ajaxSettings.xhr();
            if (xhrobj.upload) {
                    xhrobj.upload.addEventListener('progress', function(event) {
                        var percent = 0;
                        var position = event.loaded || event.position;
                        var total = event.total;
                        if (event.lengthComputable) {
                            percent = Math.ceil(position / total * 100);
                        }
                        //Set progress
                        fUploadStatusbar.setProgress(percent);
                    	console.log("status:"+percent);
                    }, false);
                }
            return xhrobj;
        },
        url: uploadURL,
        type: "POST",
        contentType:false,
        processData: false,
        cache: false,
        data: formData,
        success: function(data){
            fUploadStatusbar.setProgress(100);
            fUploadStatusbar.statusbar.addClass("done");

			fUploadStatusListCt--;

			if(fUploadStatusListCt==0)
			{
				setTimeout(
				  function() 
				  {
				    window.location.reload();
				  }, 1000);

			}


 			console.log("done");
        }
    }); 
 
 //   status.setAbort(jqXHR);
}

var fUploadStatusListCt = 0;

function createFUploadStatusbar(container){
	fUploadStatusListCt++;

	this.statusbar = $("<li></li>");
	this.filename = $("<span class='fUploadStatus-name'></span>").appendTo(this.statusbar);
	this.info = $("<span class='fUploadStatus-info'>0%</span>").appendTo(this.statusbar);
	this.progressbar = $("<span class='fUploadStatus-progress'></span>").appendTo(this.statusbar);
	container.append(this.statusbar);

	this.setFileName = function(name){
		this.filename.html(name);
	}

	this.setProgress = function(progress){

		if(progress==100)
			this.info.html("done!");
		else
			this.info.html(progress+"%");
		
		this.progressbar.animate({width:'"'+progress+'%"'},10);
	}

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