<?php
date_default_timezone_set('Asia/Taipei');

require_once('pathManage.php');

$versionDate = $_POST['ver'];
$versionDate_shown = date("Y/m/d H:i:s",strtotime($versionDate));

$historyContentfile = $dataHome.$sitePath."/versions/content.json_".$versionDate;
$contentToRecovery = json_decode(file_get_contents($historyContentfile),true);
//wait to fix
$userID = "mikeliu32";


//get meta Data last edit date
$metafile = $dataHome.$sitePath."/metainfo.json";
$metainfo = json_decode(file_get_contents($metafile));
$lastEditDate = $metainfo->lastEdit;
$lastEditDateStr = date("Y-m-d\TH:i:s", strtotime($lastEditDate));

//generate new edit date, and update metainfo
$curTime = time();
$newEditDate = date("Y-m-d\TH:i:s",$curTime);
$newEditDate_shown = date("Y/m/d H:i:s",$curTime);
$metainfo->lastEdit = $newEditDate_shown;

//make history version copy
copy($dataHome.$sitePath."/content.json", $dataHome.$sitePath."/versions/content.json_".$lastEditDateStr);

//recover old version to current version
$file = fopen($dataHome.$sitePath."/content.json","w"); //開啟檔案
fwrite($file,json_encode($contentToRecovery,JSON_UNESCAPED_UNICODE));
fclose($file);

//update meta lastEdit date
$file = fopen($dataHome.$sitePath."/metainfo.json","w"); //開啟檔案
fwrite($file,json_encode($metainfo,JSON_UNESCAPED_UNICODE));
fclose($file);

//append edit history
$historyfile = $dataHome.$sitePath."/history.json";
$historyinfo = json_decode(file_get_contents($historyfile));

$newHistory = array();
$newHistory['editor']=$userID;
$newHistory['action']='r';
$newHistory['fromVer'] = $versionDate_shown;
$newHistory['date']=$newEditDate;

array_push($historyinfo->history, $newHistory);

$file = fopen($dataHome.$sitePath."/history.json","w"); //開啟檔案
fwrite($file,json_encode($historyinfo,JSON_UNESCAPED_UNICODE));
fclose($file);

//update search index
$contentToRecovery['lastEdit'] =$newEditDate_shown;
updateEntry($entryID, $contentToRecovery);

$response=array();
$response['status']='ok';
$response['lastEditDate'] = $newEditDate_shown;

echo json_encode($response);


function updateEntry($entryID, $contentJObj){
$elasticUrl_updateUrl = "http://gaisq.cs.ccu.edu.tw:9200/nupedia/entry/".$entryID."/_update";

$docContent = array();
$docContent['doc'] = $contentJObj;

$ch = curl_init($elasticUrl_updateUrl);

curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($docContent,JSON_UNESCAPED_UNICODE) );
$result = curl_exec($ch);

curl_close($ch);  // Seems like good practice

$resultJObj = json_decode($result);

//return $resultJObj->created;
}
?>