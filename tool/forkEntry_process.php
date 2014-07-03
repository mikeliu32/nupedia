<?php
date_default_timezone_set('Asia/Taipei');

include_once('inc/auth.php');
include_once('pathManage.php');

if($IS_LOGIN && $IS_ENTRY_EXIST && isset($_POST['newEntryTitle'])){

	$newEntryTitle = $_POST['newEntryTitle'];

	$sAuthor = $_POST['sAuthor'];
	$sEid = $entryID;
	$sTitle = $_POST['sTitle'];

	$forkSrc = $dataHome.$sitePath;
	
	$srcMetaFile = $forkSrc."/metainfo.json";
	$srcMetainfoJobj = json_decode(file_get_contents($srcMetaFile));

	$srcContentFile = $forkSrc."/content.json";
	$srcContent = file_get_contents($srcContentFile);
	$srcContentJobj = json_decode($srcContent);
	
	$newEntryID = getRandomEntryID();
	$newEntryPath = $dataHome.$USER_ID."/".$newEntryID;

	//make new entry root folder
	mkdir($newEntryPath, 0775);
	chmod($newEntryPath, 0775);

	//make default images folder for entry
	mkdir($newEntryPath."/images", 0775);
	chmod($newEntryPath."/images", 0775);

	//make default files folder for entry
	mkdir($newEntryPath."/files", 0775);
	chmod($newEntryPath."/files", 0775);

	//make default versions folder for entry
	mkdir($newEntryPath."/versions", 0775);
	chmod($newEntryPath."/versions", 0775);

	//make default images/videos folder for new entry root folder
	mkdir($newEntryPath."/files/videos", 0775);
	chmod($newEntryPath."/files/videos", 0775);

	mkdir($newEntryPath."/files/pictures", 0775);
	chmod($newEntryPath."/files/pictures", 0775);

	$curTime = time();
	$createdDate_raw = date("Y-m-d\TH:i:s",$curTime);
	$createdDate = date("Y/m/d H:i:s",$curTime);

	$newMetaInfoJObj = renewMetainfoFromSrc($srcMetainfoJobj, $USER_ID, $newEntryID, $newEntryTitle, $createdDate);

	//copy and modify matainfo File from src
	$fp = fopen($newEntryPath.'/metainfo.json', 'w');
	fwrite($fp, json_encode($newMetaInfoJObj,JSON_UNESCAPED_UNICODE));
	fclose($fp);

	//copy content File from src
	$fp = fopen($newEntryPath.'/content.json', 'w');
	fwrite($fp, $srcContent );
	fclose($fp);

	//create history File (include forked info)
	$fp = fopen($newEntryPath.'/history.json', 'w');
	fwrite($fp, getHistoryJsonStr_withFork($USER_ID, $createdDate_raw, $sAuthor, $sEid, $sTitle) );
	fclose($fp);

	indexNewEntry_withContent($newEntryID, $newMetaInfoJObj, $srcContentJobj);

	$response=array();
	$response['status']='ok';
	$response['redirectEntrySite']="$USER_ID/$newEntryID";

	echo json_encode($response);
		
}



function getRandomEntryID(){
	$newFolderName = uniqid("entry_").mt_rand(10000,99999);
	
	return $newFolderName;
}


function renewMetainfoFromSrc($srcMetainfo, $newAuthor, $entryID, $entryTitle, $createdDate){

$srcMetainfo->eid = $entryID;
$srcMetainfo->title = $entryTitle;
$srcMetainfo->etitle ="";
$srcMetainfo->image = "";
$srcMetainfo->author = $newAuthor;
$srcMetainfo->collaborator = "";
$srcMetainfo->isVisible = 0;
$srcMetainfo->isForkable = 1;
$srcMetainfo->lastEdit = $createdDate;
$srcMetainfo->favCount = 0;
$srcMetainfo->viewCount = 0;



return $srcMetainfo;
}

function getHistoryJsonStr_withFork($author, $createdDate_raw, $sAuthor, $sEid, $sTitle){

$historyJson = array();
$historyJson['history'] = array();

$historyRecord_C = array();
$historyRecord_C['editor']= $author;
$historyRecord_C['action']= 'c';
$historyRecord_C['date']= $createdDate_raw;

$historyJson['history'][] = $historyRecord_C;

$historyRecord_F = array();
$historyRecord_F['editor']= $author;
$historyRecord_F['action']= 'f';
$historyRecord_F['date']= $createdDate_raw;
$historyRecord_F['sAuthor']= $sAuthor;
$historyRecord_F['sEid']= $sEid;
$historyRecord_F['sTitle']= $sTitle;

$historyJson['history'][] = $historyRecord_F;

return json_encode($historyJson,JSON_UNESCAPED_UNICODE);
}

function indexNewEntry_withContent($entryID, $metaJObj, $contentJObj){
$elasticUrl_index = "http://gaisq.cs.ccu.edu.tw:9200/nupedia/entry/";

$indexNewEntryUrl = $elasticUrl_index.$entryID;

$ch = curl_init($indexNewEntryUrl);

curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array_merge((array)$metaJObj, (array)$contentJObj)) );
$result = curl_exec($ch);

curl_close($ch);  // Seems like good practice

$resultJObj = json_decode($result);

return $resultJObj->created;
}
?>