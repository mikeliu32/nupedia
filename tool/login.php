<?php
include_once('inc/auth.php');
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="css/nupedia_all.css" type="text/css">
<link rel="stylesheet" href="css/normalize.css" type="text/css">
<link rel="stylesheet" href="css/fonts.css" type="text/css">
<link rel="stylesheet" href="css/loginpage.css" type="text/css">
<link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" type="text/css">
<!--script src="http://code.jquery.com/jquery-1.10.2.min.js"></script-->
<script src="js/jquery-1.11.0.min.js"></script>

<title>NuPedia</title>
</head>

<body>
<?php
require_once('header.php');
?>
<main class="main-wrapper">
	<div class="card">
		<form id="loginForm" class="stackForm">
			<label for="username">帳號</label>
			<input id="username" name="username" type="text"></input>
			
			<label for="userpw">密碼</label>
			<input id="userpw" name="userpw" type="password"></input>
			<button type="submit">登入</button>
			<div class="loginMsg"></div>
		</form>
	</div>
	<div class="tipsTitle">或註冊新帳號</div>
	<div class="card">
		<form id="registerForm" class="stackForm">
			<label for="newname">新帳號</label>
			<input id="newname" name="newname" type="text"></input>
			
			<label for="newpw">新密碼</label>
			<input id="newpw" name="newpw" type="password"></input>

			<label for="newpwc">確認密碼</label>
			<input id="newpwc" name="newpwc" type="password"></input>
			
			<button type="submit">註冊</button>
			<div class="loginMsg"></div>
		</form>
	</div>
</main>
<script type="text/javascript">
$( document ).ready(function() {

	$( "#loginForm" ).submit(function( e ) {
	    e.preventDefault();
		
		var uname = $("#username").val();
		var upw = $("#userpw").val();
		
		var request = $.ajax({
		  url: "inc/login_auth.php?action=i",
		  type: "POST",
		  data: { username : uname, userpw : upw },
		  dataType: "json"
		});
		 
		request.done(function( jData ) {

			if(jData.status=='ok'){
				window.location = "user.php";
			}
			else{
				$("#loginForm .loginMsg").html("密碼錯誤，請重試");
				$("#loginForm .loginMsg").show();
	
			}
		});
		 
		request.fail(function( jqXHR, textStatus ) {
		  alert( "Request failed: " + textStatus );
		});
		
		return false;
	});
	
	$( "#registerForm" ).submit(function( e ) {
	    e.preventDefault();
		
		var nname = $("#newname").val();
		var npwd = $("#newpw").val();
		var npwdc = $("#newpwc").val();
		
		
		if(npwd!==npwdc){
			$("#registerForm .loginMsg").removeClass("loginMsg-success");
			$("#registerForm .loginMsg").html("兩次密碼不相符");
			$("#registerForm .loginMsg").show();
			$("#newpw").val("");
			$("#newpwc").val("");
		}
		
		else{
		
			var request = $.ajax({
			  url: "inc/login_auth.php?action=r",
			  type: "POST",
			  data: { n_username : nname, n_userpw : npwd },
			  dataType: "json"
			});
			 
			request.done(function( jData ) {

				if(jData.status=='ok'){
					$("#registerForm .loginMsg").addClass("loginMsg-success");
					$("#registerForm .loginMsg").html(jData.userId+"註冊成功!");
					$("#registerForm .loginMsg").show();
					
					$("#newname").val("");
					$("#newpw").val("");
					$("#newpwc").val("");
				}
				else{
					$("#registerForm .loginMsg").removeClass("loginMsg-success");
					$("#registerForm .loginMsg").html("該帳號已存在，請重試");
					$("#registerForm .loginMsg").show();
					
					$("#newname").val("");
					$("#newpw").val("");
					$("#newpwc").val("");
		
				}
			});
			 
			request.fail(function( jqXHR, textStatus ) {
			  alert( "Request failed: " + textStatus );
			});
		
		}
		return false;
	});



});

</script>

</body>

</html>