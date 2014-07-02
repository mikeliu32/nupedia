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

?>