<?php
include_once('inc/auth.php');
include_once('pathManage.php');

if($IS_LOGIN && $IS_ENTRY_EXIST){

	$metafile = $dataHome.$sitePath."/metainfo.json";
	$metainfo = json_decode(file_get_contents($metafile));
	
	$entryAuthor = $metainfo->author;
	$entryTitle = $metainfo->title;
	$entryImage = $metainfo->image? $dataHome.$sitePath."/images/".$metainfo->image : "defaultPic.png";
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
<link rel="stylesheet" href="css/nupedia_all.css" type="text/css">
<link rel="stylesheet" href="css/normalize.css" type="text/css">
<link rel="stylesheet" href="css/fonts.css" type="text/css">
<link rel="stylesheet" href="css/forkpage.css" type="text/css">
<link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" type="text/css">
<!--script src="http://code.jquery.com/jquery-1.10.2.min.js"></script-->
<script src="js/jquery-1.11.0.min.js"></script>

<title>建立分支條目 | NuPedia</title>
</head>

<body>
<?php
require_once('header.php');
?>
<main class="main-wrapper">
	<div class="row">
	<div class="card">
		<img src="<?php echo $entryImage;?>"></img>
		<h2><?php echo $entryTitle;?></h2>
		<div class="author"><?php echo $entryAuthor;?></div>
	</div>
	<img src="fork.png" class="fork-img"/>
	<div class="card forkTo">
		<img src="defaultPic.png"></img>
		<h2><input type="text" id="newEntryTitle" placeholder="輸入分支條目名稱"></input></h2>
		<div class="author"><?php echo $USER_ID;?></div>
	</div>
	</div>
	<div class="row">
		<button id="saveBtn" class="btn save">建立分支條目</a>
		<button id="exitBtn" class="btn cancel">離開</a>
	</div>
	
</main>
<script type="text/javascript">
$( document ).ready(function() {


	$("#saveBtn").click(function(e){
		e.preventDefault();
		
		var newEntryTitle = $("#newEntryTitle").val();
		
		var request = $.ajax({
		  url: "forkEntry_process.php?site=<?php echo $sitePath;?>",
		  type: "POST",
		  data: { newEntryTitle: newEntryTitle, sAuthor : '<?php echo $entryAuthor;?>', sTitle: '<?php echo $entryTitle;?>'},
		  dataType: "json"
		});
		 
		request.done(function( jData ) {

			if(jData.status=='ok'){

				window.location = 'index.php?site='+jData.redirectEntrySite;
			}
			else{

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


});

</script>

</body>

</html>