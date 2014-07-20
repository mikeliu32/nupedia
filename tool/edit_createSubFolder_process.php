<?php
include_once('inc/auth.php');
include_once('pathManage.php');

if($IS_LOGIN && $IS_ENTRY_EXIST && isset($_POST['eid']) ){

	$postEid = $_POST['eid'];
	$newFolder = $_POST['folderName'];
	$secID = $_POST['secID'];

	$metafile = $dataHome.$sitePath."/metainfo.json";
	$metainfo = json_decode(file_get_contents($metafile));

	$IS_AUTHOR = isAuthor($metainfo->author);
	
	if($IS_AUTHOR && strcmp($entryID,$postEid)==0){
	
		$filePath = $dataHome.$sitePath."/files/";
		
			if(!is_dir($filePath.$newFolder)){
			
				mkdir($filePath.$newFolder, 0775);
				chmod($filePath.$newFolder, 0775);
				
				$response=array();
				$response['status']='ok';
				$response['sitePath']=$sitePath;
				$response['folderName']=urlencode($newFolder);
				$response['secID']=$secID;
				echo json_encode($response,JSON_UNESCAPED_UNICODE);
				die();
			
			}
			else
			{
				$response=array();
				$response['status']='error';
				echo json_encode($response);
				die();
			}
	
	}
	else
	{
		$response=array();
		$response['status']='error';
		echo json_encode($response);
		die();
	}
}
else{
	$response=array();
	$response['status']='error';
	echo json_encode($response);
	die();
}

?>