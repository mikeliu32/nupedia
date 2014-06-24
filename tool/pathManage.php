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




?>