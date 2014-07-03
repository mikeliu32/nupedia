<?php
include_once('db_class.php');
include_once('db_config.php');
include_once('auth.php');
$db = new DB();
$db->connect_db($_DB['host'],$_DB['username'],$_DB['password'],$_DB['dbname']);


if( isset($_GET['action']) && $IS_LOGIN ){

	$action = $_GET['action'];
	
	switch($action){
		
		case 'a':
			if(isset($_POST['eid'])){
				$EID = $_POST['eid'];

				$db->query("SELECT * FROM favorite WHERE userName = '$USER_ID' AND eid = '$EID';");
				$result = $db->fetch_array();
					
				if(count($result)==0){
					$db->query("INSERT INTO favorite (userName, eid) VALUES ('$USER_ID', '$EID');");

					updateFavCount($EID, true);

					$result=array();
					$result['status']="ok";
					echo json_encode($result);
				}
			}
			break;
			
		case 'r':
			if(isset($_POST['eid'])){
				$EID = $_POST['eid'];

				$db->query("DELETE FROM favorite WHERE userName = '$USER_ID' AND eid = '$EID';");

				updateFavCount($EID, false);

				$result=array();
				$result['status']="ok";
				echo json_encode($result);
			}
			break;
		
		case 'c':
			if(isset($_POST['eid'])){
				$EID = $_POST['eid'];

				$db->query("SELECT * FROM favorite WHERE userName = '$USER_ID' AND eid = '$EID';");
				$result = $db->fetch_array();
					
				if(count($result)){	
					$result=array();
					$result['isFavorite']=true;
					echo json_encode($result);
				}
				else{
					$result=array();
					$result['isFavorite']=false;
					echo json_encode($result);
				}
			}
			break;
	
	}

}


function updateFavCount($entryID, $isAdd){
$elasticUrl_updateUrl = "http://gaisq.cs.ccu.edu.tw:9200/nupedia/entry/".$entryID."/_update";

$docContent = array();
$docContent['script'] = ($isAdd)? "ctx._source.favCount+=1" : "ctx._source.favCount+=-1";

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