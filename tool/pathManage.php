<?php
$dataHome = '../npdata/';

if(isset($_GET['site'])){
	
	$sitePath = $_GET['site'];
	$siteTok = explode("/",$sitePath); 
	$entryID = $siteTok[1];
}
else{
	$sitePath="";
	$entryID= "";
}

$IS_ENTRY_EXIST = (strlen($sitePath)>1 && strlen($entryID)>1 && is_dir($dataHome.$sitePath));





?>