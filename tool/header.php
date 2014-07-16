<header class="headerBar">
<a href="../index.php"><img src="nupedia_logo.png" class="headerBar-logo"/></a>

<form id="headerBar-searchForm" action="search.php" method="get">
<input id="headerBar-searchInput" name="q" type="text" placeholder="搜尋NUPedia"></input>
<input id="headerBar-searchSubmit" type="submit"></input>
</form>

<span id="headerBar-user">
<?php
	if($IS_LOGIN){
		echo "<a href=\"user.php\"><i class=\"icon-user\"></i> <span> ".$USER_ID."</span></a>";
	}
	else{
		echo "<a href=\"login.php\"><i class=\"icon-user\"></i><span>登入</span></a>";
	}
?>
</span>

<ul id="headerBar-nav">
<li><a href="category.php">分類瀏覽</a></li>
<li><a href="trend.php">熱門條目</a></li>
</ul>

</header>