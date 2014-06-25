<?php
date_default_timezone_set('Asia/Taipei');

include_once('inc/auth.php');

if(!$IS_LOGIN){
	header("Location: error.php");
	die();
}

if(isset($_GET['entryTitle'])){
 
$dataRoot = "../npdata/";

$newEntryTitle = $_GET['entryTitle'];

$newEntryID = getRandomEntryID();
$newEntryPath = $dataRoot.$USER_ID."/".$newEntryID;

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

$metaInfoJStr = getMetaInfoJsonStr($USER_ID, $newEntryID, $newEntryTitle, $createdDate);

//create default matainfo File
$fp = fopen($newEntryPath.'/metainfo.json', 'w');
fwrite($fp, $metaInfoJStr);
fclose($fp);

//create default content File
$fp = fopen($newEntryPath.'/content.json', 'w');
fwrite($fp, getContentJsonStr() );
fclose($fp);

//create default history File
$fp = fopen($newEntryPath.'/history.json', 'w');
fwrite($fp, getHistoryJsonStr($USER_ID, $createdDate_raw) );
fclose($fp);

indexNewEntry($newEntryID, $metaInfoJStr);

$response=array();
$response['status']='ok';
$response['redirectEntrySite']="$USER_ID/$newEntryID";

echo json_encode($response);

}

function getRandomEntryID(){
	$newFolderName = uniqid("entry_").mt_rand(10000,99999);
	
	return $newFolderName;
}


function getMetaInfoJsonStr($author, $entryID, $entryTitle, $createdDate){

$metaJson = array();
$metaJson['eid'] = $entryID;
$metaJson['title']= $entryTitle;
$metaJson['etitle']="";
$metaJson['image']="";
$metaJson['meta']=array();
$metaJson['author'] = $author;
$metaJson['collaborator']="";
$metaJson['lastEdit'] = $createdDate;

return json_encode($metaJson,JSON_UNESCAPED_UNICODE);
}

function getContentJsonStr(){

$contentJson = array();
$contentJson['abstract']= "";
$contentJson['sections']= array();

return json_encode($contentJson,JSON_UNESCAPED_UNICODE);
}

function getHistoryJsonStr($author, $createdDate_raw){

$historyJson = array();
$historyRecord = array();
$historyRecord['editor']= $author;
$historyRecord['action']= 'c';
$historyRecord['date']= $createdDate_raw;

$historyJson['history'] = array();
$historyJson['history'][] = $historyRecord;

return json_encode($historyJson,JSON_UNESCAPED_UNICODE);
}

function indexNewEntry($entryID, $metaJStr){
$elasticUrl_index = "http://gaisq.cs.ccu.edu.tw:9200/nupedia/entry/";

$indexNewEntryUrl = $elasticUrl_index.$entryID;

$ch = curl_init($indexNewEntryUrl);

curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $metaJStr);
$result = curl_exec($ch);

curl_close($ch);  // Seems like good practice

$resultJObj = json_decode($result);

return $resultJObj->created;
}




?>