<?php

include_once('inc/auth.php');
include_once('pathManage.php');

if($IS_LOGIN && $IS_ENTRY_EXIST && isset($_POST['eid']) ){

	$postEid = $_POST['eid'];
	
	$metafile = $dataHome.$sitePath."/metainfo.json";
	$metainfo = json_decode(file_get_contents($metafile));

	$IS_AUTHOR = isAuthor($metainfo->author);
	
	if($IS_AUTHOR && strcmp($entryID,$postEid)==0){
		
		$targetPath = $dataHome.$sitePath;

		//remove dir recursivly
		delete_directory($targetPath);
		//remove elastic search index
		deleteIndex($entryID);
	
		$response=array();
		$response['status']='ok';
		echo json_encode($response);
		die();
	}
	else{
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
/*
function delete_files($target) {
    if(is_dir($target)){
        $files = glob( $target . '*', GLOB_MARK ); //GLOB_MARK adds a slash to directories returned
        
        foreach( $files as $file )
        {
            delete_files( $file );      
        }
      
        rmdir( $target );
    } elseif(is_file($target)) {
        unlink( $target );  
    }
}
*/
function delete_directory($dirname) {
         if (is_dir($dirname))
           $dir_handle = opendir($dirname);
	 if (!$dir_handle)
	      return false;
	 while($file = readdir($dir_handle)) {
	       if ($file != "." && $file != "..") {
	            if (!is_dir($dirname."/".$file))
	                 unlink($dirname."/".$file);
	            else
	                 delete_directory($dirname.'/'.$file);
	       }
	 }
	 closedir($dir_handle);
	 rmdir($dirname);
	 return true;
}

function deleteIndex($entryID){
$elasticUrl_Entry_Url = "http://gaisq.cs.ccu.edu.tw:9200/nupedia/entry/".$entryID;


$ch = curl_init($elasticUrl_Entry_Url);

curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$result = curl_exec($ch);

curl_close($ch);  // Seems like good practice

$resultJObj = json_decode($result);

return $resultJObj;
}

?>