<?php


$resultAry=array();

if(isset($_GET['size'])&&$_GET['size']!=NULL){
	$resultSize = $_GET['size'];
	
	$srObj = getHotEntries($resultSize);
	
	$resultAry = $srObj->hits->hits;

	$hotlist = array();
	
	foreach($resultAry as $result){
	
		$entry = $result->_source;
		
		$entryToAdd = array();
		
		$entryToAdd['eid'] = $entry->eid;
		$entryToAdd['author'] = $entry->author;
		$entryToAdd['image'] = $entry->image;
		$entryToAdd['title'] = $entry->title;
		$entryToAdd['tag'] = $entry->tag;
		$entryToAdd['lastEdit'] = $entry->lastEdit;
		$entryToAdd['abs_plain'] = mb_strimwidth($entry->abstract_plain, 0, 300, "...", "UTF-8");
		
		$entryToAdd['sitePath']= $entry->author."/".$entry->eid; 
		
		$hotlist[] = $entryToAdd;
	}
	
	echo json_encode($hotlist);
	
}




function getHotEntries($size){

$elasticUrl_search = "http://gaisq.cs.ccu.edu.tw:9200/nupedia/entry/_search?q=(isVisible:1)&sort=viewCount:desc,favCount:desc";

$searchUrl = $elasticUrl_search."&size=".$size;

$ch = curl_init($searchUrl);

curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);

curl_close($ch);  // Seems like good practice

$resultJObj = json_decode($result);

return $resultJObj;
}

?>