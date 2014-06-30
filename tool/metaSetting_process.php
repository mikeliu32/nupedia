<?php
require_once('pathManage.php');

if(isset($_GET['type'])){

	$type = $_GET['type'];

	
	switch($type){
		// change metaSetting
		case 'm':
		
			$metafile = $dataHome.$sitePath."/metainfo.json";
			$metainfo = json_decode(file_get_contents($metafile));

			$meta = $metainfo->meta;

			$nTitle = $_POST['title'];
			$nEtitle = $_POST['etitle'];
			$rawMeta = $_POST['meta'];
			$collabs = $_POST['collaborator'];
			$tags = $_POST['tag'];
			
			$nMeta = array();
			for($i=0;$i<count($rawMeta);$i++){

				$metaCol=array();
				$metaCol['name']=$rawMeta[$i]['name'];
				$metaCol['value']=$rawMeta[$i]['value'];

				$nMeta[]=$metaCol;
			}
			
			$metainfo->title = $nTitle;
			$metainfo->etitle = $nEtitle;
			$metainfo->meta = $nMeta;
			$metainfo->collaborator = $collabs;
			$metainfo->tag = $tags;
			
			$file = fopen($dataHome.$sitePath."/metainfo.json","w"); //開啟檔案
			fwrite($file,json_encode($metainfo,JSON_UNESCAPED_UNICODE));
			fclose($file);

			updateEntry($entryID, $metainfo);
			
			$response=array();
			$response['status']='ok';

			echo json_encode($response);
	
			break;
			
		//upload image
		case 'i':
			$valid_exts = array('jpeg', 'jpg', 'png', 'gif'); // valid extensions
			$max_size = 200 * 1024; // max file size
			$imgPath = $dataHome.$sitePath.'/images/'; // upload directory

			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			  if( ! empty($_FILES['image']) ) {
				// get uploaded file extension
				$ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
				// looking for format and size validity
				if (in_array($ext, $valid_exts) AND $_FILES['image']['size'] < $max_size) {
				  $newName = uniqid().mt_rand(1000,9999).'.'.$ext; 
				  $imgPath = $imgPath.$newName;
				  // move uploaded file from temp to uploads directory
				  if (move_uploaded_file($_FILES['image']['tmp_name'], $imgPath)) {

					$metafile = $dataHome.$sitePath."/metainfo.json";
					$metainfo = json_decode(file_get_contents($metafile));

					$metainfo->image = $newName;

					$file = fopen($dataHome.$sitePath."/metainfo.json","w"); //開啟檔案
					fwrite($file,json_encode($metainfo,JSON_UNESCAPED_UNICODE));
					fclose($file);

					updateEntry($entryID, $metainfo);
					
					$response=array();
					$response['status']='ok';
					$response['image']=$newName;

					echo json_encode($response);
				  
				  }
				} else {
					$response=array();
					$response['status']='error';
					$response['msg']='檔案格式不符';

					echo json_encode($response);
				}
			  } else {
				$response=array();
				$response['status']='error';
				$response['msg']='檔案上傳錯誤';

				echo json_encode($response);
			  }
			} else {
				$response=array();
				$response['status']='error';
				$response['msg']='Bad request!';

				echo json_encode($response);
			}
		
		break;
		
	}


}

function updateEntry($entryID, $metaJObj){
$elasticUrl_updateUrl = "http://gaisq.cs.ccu.edu.tw:9200/nupedia/entry/".$entryID."/_update";

$docContent = array();
$docContent['doc'] = $metaJObj;

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