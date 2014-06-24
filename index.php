<?php
include_once('tool/inc/auth.php');
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="tool/css/normalize.css" type="text/css">
<link rel="stylesheet" href="tool/css/nupedia_all.css" type="text/css">
<link rel="stylesheet" href="index.css" type="text/css">
<link rel="stylesheet" href="tool/css/fonts.css" type="text/css">

<link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" type="text/css">
<!--script src="http://code.jquery.com/jquery-1.10.2.min.js"></script-->
<script src="tool/js/jquery-1.11.0.min.js"></script>

<title>NuPedia</title>
</head>

<body>
<div class="bg"></div>
<nav>
<ul>
    <li><a href="#">分類瀏覽</a></li>
    <li><a href="#">熱門條目</a></li>
<?php
	if($IS_LOGIN):
?>
		<li><a href="tool/user.php?u=<? echo $USER_ID;?>"><?php echo $USER_ID;?></a></li>
<?php
	else:
?>
		<li><a href="tool/login.php">登入</a></li>
<?php
	endif;
?>
</ul>
</nav>
<main class="main-wrapper">
<img src="tool/nupedia_logo.png"/>
<form class="searchForm" action="tool/search.php">
	<input name="q" type="text"></input>
	<input type="submit" value="搜尋"></input>
</form>
</main>
<footer>
<div><a href="https://www.flickr.com/photos/nasa2explore/14219563670">Photo</a> by NASA: 2Explore / <a href="http://creativecommons.org/licenses/by-nc/2.0/">CC BY-NC</a></div>
<div>&copy;2014 Mike Liu@GAISLab, All Rights Reserved.</div>
</footer>
<script type="text/javascript">
$( document ).ready(function() {

});

</script>

</body>

</html>