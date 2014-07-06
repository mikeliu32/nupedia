<?php

if( isset($_GET['keyword']) ){

	$keyword = $_GET['keyword'];
	
	if(isset($_GET['size']))
		$size = $_GET['size'];
	else
		$size = 10;
		
	$result = getRelatedTerm($keyword, $size);
	
	echo json_encode($result);
}


function getRelatedTerm($keyword, $size){

include_once('db_class.php');
include_once('db_config.php');

$db = new DB();
$db->connect_db($_DB['host'],$_DB['username'],$_DB['password'],$_DB['dbname']);

$db->query("SELECT * FROM `relatedCorpus` WHERE `keyword`='$keyword' ORDER BY `score` DESC LIMIT $size;");

$resultAry = array();
$resultAry['keyword']=$keyword;
$resultAry['results']=array();

$relatedwordCt=0;

while($row = $db->fetch_array()){
	$result = array();
	$result['relatedword'] = $row['relatedword'];
	$result['score'] = $row['score'];
	
	$resultAry['results'][] = $result;
	$relatedwordCt++;
}

$resultAry['count'] = $relatedwordCt;

return $resultAry;
//return $resultJObj->created;
}


?>