<?php
session_start();
$IS_LOGIN = isLogin();
$USER_ID = getUserId();

function getUserId(){

	if(isset($_SESSION['isLogin']) && isset($_SESSION['userId']))
		return $_SESSION['userId'];
	else
		return null;
}

function isLogin(){

	if(isset($_SESSION['isLogin']) && $_SESSION['isLogin'])
		return true;
	else
		return false;
}

function isAuthor( $authorToMatch ){

	if(isset($_SESSION['isLogin']) && isset($_SESSION['userId'])){
		if(strcmp($_SESSION['userId'], $authorToMatch)==0)
			return true;
		else
			return false;
	}
	else
		return false;

}
?>