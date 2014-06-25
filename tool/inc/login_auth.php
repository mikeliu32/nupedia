<?php
include_once('db_class.php');
include_once('db_config.php');

$db = new DB();
$db->connect_db($_DB['host'],$_DB['username'],$_DB['password'],$_DB['dbname']);


if(isset($_GET['action'])){


	switch($_GET['action']){
	
		case 'r':
			$newUsername = $_POST['n_username'];
			$newPW = $_POST['n_userpw'];
	
			$newPW = hash("sha256", $newPW);
			
			$db->query("SELECT * FROM user WHERE userName='$newUsername';");
			$result = $db->fetch_array();
			
			if(count($result)){
				$result=array();
				$result['status']="fail";
				echo json_encode($result);
			}
			else{
			
				$db->query("INSERT INTO user (userName, userPassword) VALUES ('$newUsername', '$newPW');");
			
				createUserFolder($newUsername);
			
				$result=array();
				$result['status']="ok";
				$result['userId']=$newUsername;
				echo json_encode($result);
			}
			
			break;
		
		case 'i':
			$username = $_POST['username'];
			$pw = $_POST['userpw'];
	
			$pw = hash("sha256", $pw);
			
			$db->query("SELECT * FROM user WHERE userName='$username' AND userPassword='$pw';");
			
			$result = $db->fetch_array();
			
		
			if(count($result)){

				//save user info
				session_start();
				$_SESSION['isLogin'] = true;
				$_SESSION['userId'] = $username;

				$result=array();
				$result['status']="ok";
				$result['userId']=$username;
				echo json_encode($result);
			}
			else{
				$result=array();
				$result['status']="fail";
				echo json_encode($result);
			}
			break;
		
		case 'o':
			session_start();
			session_destroy();
			header('Location: ../login.php') ;
			break;
		

	}
}

function createUserFolder($userId){
$dataRoot = "../../npdata/";

$newUserFolder = $dataRoot.$userId;

//make new user root folder
mkdir($newUserFolder, 0775);
chmod($newUserFolder, 0775);
}
?>