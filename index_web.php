<?php
	session_start();
	header("Content-Type:text/html; charset=utf-8");
	require_once('inc_funcs.php');
	date_default_timezone_set('Asia/Taipei');
	/*
	echo urlencode("政治");
	echo "<p></p>";
	echo urldecode("%E6%94%BF%E6%B2%BB");
	*/	

	$kernal = @$_POST['select_kernal'];
	$types = @$_POST['select_types'];
	$sort = @$_POST['select_sort'];
	$size = @$_POST['select_size'];
	$keyword = urlencode(@$_POST['keywordtext']);
	$key = @$_POST['keywordtext'];
	$k = @$_GET['k'];
	$other = "";
	
	if( $k != "" ){
		$keyword = urlencode(@$k);
		$key = @$k;
		$kernal = "Both";
		$types = "web";
		$sort = "Auto";
		$size = 10;
	}
	
	function getkeys(){
		$r_value="";
		$conn = mysql_connect("mysql.cs.ccu.edu.tw", "mjc101m", "fortune526");

		if (!$conn) {
			echo "Unable to connect to DB: " . mysql_error();
			exit;
		}

		if (!mysql_select_db("mjc101m_kernalsearch")) {
			echo "Unable to select mydbname: " . mysql_error();
			exit;
		}

		$sql = "SELECT * FROM  search_keys ";

		$result = mysql_query($sql);
		
		while ($row = mysql_fetch_assoc($result)) {
			$r_value = $r_value." <li><a href='index_web.php?k=".$row["keyword"]."'>".$row["keyword"]."<button type='button' class='close' data-dismiss='alert' style='float:none;font-size:18px;'>×</button></a></li>";
		}

		return $r_value;
    }
	
	
	//Google Search Parsing
	function k_google($l,$r){
		global $kernal, $types, $sort, $size, $keyword,$key;
		$other = "";
		/*
		if( $sort != "Auto" ){
			if( $sort == "ASC" ){
				$other = $other."&sort=id:asc";
			}
			else if( $sort == "DESC" ){
				$other = $other."&sort=id:desc";
			}
		}
		$other = $other."&size=".$size;
		*/
        
        /*
		if( $filders == "all" ){
            $g_url = "http://gaisk.cs.ccu.edu.tw:9200/Googlekernal/source/_search?q=".$keyword.$other;
        }
        else{
            $g_url = "http://gaisk.cs.ccu.edu.tw:9200/Googlekernal/source/_search?q=".$filders.":".$keyword.$other;
        }
		
		$othercount=0;
        */
        $g_url = "http://www.google.com.tw/search?q=".$keyword."&num=".$size."&oe=utf-8";  //size設定回傳結果筆數   oe設定回傳結果編碼
        //$g_url = "https://www.google.com.tw/search?q=%E6%94%BF%E6%B2%BB&num=5&oe=utf-8";

        $curl = curl_init($g_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $g_html = curl_exec($curl);
        curl_close($curl);

        //$g_content = big52utf8($g_html); //iconv("big5","utf-8",$g_html);
		
		//先用<li class="g">切割成一筆一筆放入array
        $sr_g_content = my_reg_split($g_html,'<li class="g">');//count($sr_g_content)
		$g_solution="";
		$o_num=1;
		
		for($i=1;$i<count($sr_g_content);$i++){
				
			preg_match("/<h3 class=\"r\"><a href=\".*?[^>]+>(.*?[^<]+)/", $sr_g_content[$i], $matches_title);
			$o_title = $matches_title[1];  //取得title
				
			if( !(preg_match('/<a href=\"\/search\?q=/', $sr_g_content[$i], $matches_search)) && !(preg_match('/<a href=\"\/images\?q=/', $sr_g_content[$i], $matches_search)) ){
				//上面 if 過濾掉新聞搜尋和圖片搜尋結果
				//print_r($matches);
				//echo $o_title."<br/>";
				preg_match("/<h3 class=\"r\"><a href=\"(.*?[^\"]+)/", $sr_g_content[$i], $matches_url);
				$o_link = @ereg_replace ('\/url\?q=','http://www.google.com/url?q=', $matches_url[1]); //取得連結  修改連結網址  因為原本抓到的連結是無法使用的
				//echo $o_link."<br/>";
				preg_match("/<span class=\"st\">(.*?[^<]+)/", $sr_g_content[$i], $matches_content);
				$o_content = @ereg_replace ('<','', $matches_content[1]);	//取得內容描述  因為regular會多一個<  所以要取代掉			
				//echo $o_content."<br/>";
				//echo "<textarea style='width:1000px;height:150px;'>".$o_title."</textarea><br/>";
				

				//以下是我顯示的處理  原始資料抓取在上面那些
				$id = "<font size='0.3em'>".date("Ymd").str_pad($o_num,4,"0",STR_PAD_LEFT)." __ ";
				$category = "".$key."</font><br/>";
				$title = "<font size='3em'><b><a href='".$o_link."' target='_blank'>".$o_title."</a></b></font><br/>";
				$title_little = "<font size='10em'><b><a href='".$o_link."' target='_blank'>".iconv_substr($o_title,0,18,"UTF-8")."...... </a></b></font><br/>";
				
				$content_data = "<font size='0.7em'>".$o_content."</font><br/>";
				$content_little = "<font size='0.9em'>".iconv_substr($o_content,0,20,"UTF-8")."...... </font><br/>";
				$title_content="<div id='little_c".$o_num."' style='display:block;>".$title_little.$content_little."<a href='javascript: Change_c(".$o_num."); return false;'>read more</a></div><div id='more_c".$o_num."' style='display:none;'>".$title.$content_data."<a href='javascript: Change_c(".$o_num."); return false;'>read less</a></div>";
				
				$hidden_data1 = '<input type="hidden" name="link1[]" value="'.str_replace('"', '\"', $o_link).'"/><input type="hidden" name="title1[]" value="'.str_replace('"', '\"', $o_title).'"/><input type="hidden" name="content1[]" value="'.str_replace('"', '\"', $o_content).'"/><input type="hidden" name="state1[]" value="1"/>';
				$hidden_data2 = '<input type="hidden" name="link2[]" value="'.str_replace('"', '\"', $o_link).'"/><input type="hidden" name="title2[]" value="'.str_replace('"', '\"', $o_title).'"/><input type="hidden" name="content2[]" value="'.str_replace('"', '\"', $o_content).'"/><input type="hidden" name="state2[]" value="1"/>';
				
				if( $l == 1){
					$g_solution = $g_solution."<div class='alert alert-success tile' style='margin-bottom:10px;'><button type='button' class='close' data-dismiss='alert' style='float:right;font-size:2em;' onclick='myFunction()'>×</button>".$id.$category.$title_content.$hidden_data1."</div>";
				}
				if( $r == 1){
					$g_solution = $g_solution."<div class='alert alert-info tile' style='margin-bottom:10px;'><button type='button' class='close' data-dismiss='alert' style='float:right;font-size:2em;' onclick='myFunction()'>×</button>".$id.$category.$title_content.$hidden_data2."</div>";
				}
					
				$o_num++;					
			}
			/*
			else{
				
			}
			*/
			//echo "<textarea style='width:1000px;height:150px;'>".$sr_g_content[$i]."</textarea><br/>";
		}

        //echo "<pre>".print_r($g_content)."</pre>";//exit;
        //echo "<textarea style='width:500px;height:300px'>".$g_html."</textarea><p>XXXX</p>";exit;
		//echo "<textarea style='width:500px;height:300px'>".$g_content."</textarea><p>XXXX</p>";exit;
        
        /*
		$content3 = @ereg_replace ('\"\/url\?q=','"http://www.google.com/url?q=', $content2);
		$content4 = @ereg_replace ('\"\/search\?q=','"http://www.google.com/search?q=', $content3);
		$content5 = @ereg_replace ('\"\/images\?q=','"http://www.google.com/images?q=', $content4);
		*/		
		
		return $g_solution;
	}
	
	function k_yahoo($l,$r){
		global $kernal, $types, $sort, $size, $keyword,$key;
		$other = "";
		/*
		if( $sort != "Auto" ){
			if( $sort == "ASC" ){
				$other = $other."&sort=id%20asc";
			}
			else if( $sort == "DESC" ){
				$other = $other."&sort=id%20desc";
			}
		}
		$other = $other."&size=".$size;
		*/
        $y_solution="";
		/*
        if( $filders == "all" ){
            $y_url = "http://gaisk.cs.ccu.edu.tw:8983/Yahoo/select?q=D_F:".$keyword."&wt=json".$other;
        }
        else{
		    $y_url = "http://gaisk.cs.ccu.edu.tw:8983/Yahoo/select?q=".$filders.":".$keyword."&wt=json".$other;
        }
		*/
		
		$y_url = "http://tw.search.yahoo.com/search?p=".$keyword."&n=".$size."&oe=utf-8";
		$y_html = file_get_contents($y_url);
		$y_html = @ereg_replace ('<b>','', $y_html);
		$y_html = @ereg_replace ('</b>','', $y_html);
		$y_html = @ereg_replace ('<br/>','', $y_html);
		
        $sr_y_content = my_reg_split($y_html,'<div class="res">');//count($sr_g_content)
		$y_num=1;
		
		for($i=1;$i<count($sr_y_content);$i++){
			//echo "<textarea rows='100' cols='100'>".$sr_y_content[$i]."</textarea>";
			//exit;
			preg_match("/class=\"yschttl spt\".*?[^>]>(.*?[^<]+)/", $sr_y_content[$i], $matches_title);
			$y_title = $matches_title[1];
			//echo $y_title."<br/>";
			
			preg_match("/class=\"yschttl spt\" href=\"(.*?[^\"]+)/", $sr_y_content[$i], $matches_url);
			$y_link = $matches_url[1];
			//echo $y_link."<br/>";
			if( preg_match("/<div class=\"abstr\">(.*?[^<]+)/", $sr_y_content[$i], $matches_content) ){
				$y_content = $matches_content[1];
			}
			else{ $y_content=""; }
			//echo $y_content."<br/>";
			
			$id = "<font size='0.3em'>".date("Ymd").str_pad($y_num,4,"0",STR_PAD_LEFT)." __ ";
			$category = "".$key."</font><br/>";
			$title = "<font size='3em'><b><a href='".$y_link."' target='_blank'>".$y_title."</a></b></font><br/>";
			$title_little = "<font size='10em'><b><a href='".$y_link."' target='_blank'>".iconv_substr($y_title,0,18,"UTF-8")."...... </a></b></font><br/>";
			
			$content_data = "<font size='0.7em'>".$y_content."</font><br/>";
			$content_little = "<font size='0.9em'>".iconv_substr($y_content,0,20,"UTF-8")."...... </font><br/>";
			$title_content="<div id='little_sc".$y_num."' style='display:block;>".$title_little.$content_little."<a href='javascript: Change_sc(".$y_num."); return false;'>read more</a></div><div id='more_sc".$y_num."' style='display:none;'>".$title.$content_data."<a href='javascript: Change_sc(".$y_num."); return false;'>read less</a></div>";
			
			$hidden_data1 = '<input type="hidden" name="link1[]" value="'.str_replace('"', '\"', $y_link).'"/><input type="hidden" name="title1[]" value="'.str_replace('"', '\"', $y_title).'"/><input type="hidden" name="content1[]" value="'.str_replace('"', '\"', $y_content).'"/><input type="hidden" name="state1[]" value="1"/>';
			$hidden_data2 = '<input type="hidden" name="link2[]" value="'.str_replace('"', '\"', $y_link).'"/><input type="hidden" name="title2[]" value="'.str_replace('"', '\"', $y_title).'"/><input type="hidden" name="content2[]" value="'.str_replace('"', '\"', $y_content).'"/><input type="hidden" name="state2[]" value="1"/>';
			
			if( $l == 1){
				$y_solution = $y_solution."<div class='alert alert-success tile' style='margin-bottom:10px;' id='y_data_".$y_num."'><button type='button' class='close' data-dismiss='alert' style='float:right;font-size:2em;' name='remove_y' id='".$y_num."'>×</button><span class='label label-success' style='float:right;font-size:0.2em;'><i class='icon-thumbs-up'></i>5</span>".$id.$category.$title_content.$hidden_data1."</div>";
			}
			if( $r == 1){
				$y_solution = $y_solution."<div class='alert alert-info tile' style='margin-bottom:10px;' id='y_data_".$y_num."'><button type='button' class='close' data-dismiss='alert' style='float:right;font-size:2em;' name='remove_y' id='".$y_num."'>×</button><span class='label label-info' style='float:right;font-size:0.2em;'><i class='icon-thumbs-up'></i>5</span>".$id.$category.$title_content.$hidden_data2."</div>";
			}
			
			$y_num++;
			
			//echo "<textarea style='width:1000px;height:150px;'>".$sr_y_content[$i]."</textarea><br/>";
		}
		
		//echo "<textarea style='width:1000px;height:1000px;'>".$y_content."</textarea><br/>";		
		

		return $y_solution;
	}
	
	if( $kernal == "Both"){
		$rand = rand(0,1);
		if( $rand == 0){  
			$left = "Google";
			$right = "Yahoo";
			$left_content = k_google(1,0);
			$right_content = k_yahoo(0,1); //k_google(0,1);//k_yahoo(0,1);
		}
		else{ 
			$left = "Yahoo";
			$right = "Google";
			$left_content = k_yahoo(1,0); //k_google(1,0);//k_yahoo(1,0);
			$right_content = k_google(0,1);
		}
	}	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>NewsData</title>
	<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
	<script src="http://code.jquery.com/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
	<script src="http://code.jquery.com/ui/1.10.0/jquery-ui.js"></script>
	<link rel="stylesheet" href="css/smoothness/jquery-ui-1.10.0.custom.css" />
	<link rel="stylesheet" href="css/custom.css" />
	
	<link href="jtable/themes/metro/lightgray/jtable.css" rel="stylesheet" type="text/css" />	
	<script src="js/jquery-1.9.0.js"></script>
	<script src="js/jquery-ui-1.10.0.custom.min.js"></script>
    <script src="jtable/jquery.jtable.min.js" type="text/javascript"></script>
    <script src="jtable/localization/jquery.jtable.zh-TW.js" type="text/javascript"></script>
	
	<!-- Star Score -->
	<script src="js/jquery-1.3.2.min.js" type="text/javascript"></script>
	<link href="css/rating_star.css" rel="stylesheet" type="text/css">
	<script src="js/rating_star.js" type="text/javascript"></script>

	<link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
	<script src="//code.jquery.com/jquery-1.10.2.js"></script>
	<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
	
	<style>
		.placeholder {
			border: 1px solid #d3d3d3;
			background-color: white;
			-webkit-box-shadow: 0px 0px 10px #888;
			-moz-box-shadow: 0px 0px 10px #888;
			box-shadow: 0px 0px 10px #888;
			height:58px;
		}
		.tile {
			height: 100%;
		}
		.grid {
			margin-top: 1em;
		}
	</style>

	<script type="text/javascript">

		$(document).ready(function () {
			
			$("#OptionBtn").click(function(){
				$( "#info_sf" ).toggle();
			});
			
			$("#AddKeyBtn").click(function(){
				if (confirm('確定新增此關鍵字？')) {
					//alert("true");
					$.ajax({
						type: "POST",
						url: "action.php?ac=AddKey",
						data: $("#SearchForm").serialize(), // serializes the form's elements.
						success: function(data)
						{
						   //alert("ok");
						   location.href = "index_web.php";
						   //alert("ok2"); // show response from the php script.
						}
					});
				}
			});
			
			$("#SaveBtn").click(function(){
				if (confirm('確定儲存此次結果？')) {
					//alert("true");
					$.ajax({
						type: "POST",
						url: "action.php?ac=Save",
						data: $("#SaveForm").serialize(), // serializes the form's elements.
						success: function(data)
						{
						   //alert("ok");
						   location.href = "index_web.php";
						   //alert("ok2"); // show response from the php script.
						}
					});
				}
			});
			
			$(function () {
				$(".grid").sortable({
					tolerance: 'pointer',
					revert: 'invalid',
					placeholder: 'well placeholder',
					forceHelperSize: true
				});
			});
			
			$(function() {
				$("#rating_star").webwidget_rating_sex({
					rating_star_length: '5',
					rating_initial_value: '',
					rating_function_name: '',
					directory: 'images/'
				});
			});
			
			/*
			$(function(){
				$("#lA").load("main_webblock.php");
			});
			$(function(){
				$("#lB").load("test3.php");
			});
			*/
			/*
			$("#remove_y").click(function(){
				alert(this.id);
			});*/
			
			$("button[name='remove_y']").click(function() {
				var y_id = $(this).attr('id');
				alert(y_id); // alert(this.id); or alert($(this).attr('id'));
				//return false;
			});
			
			$("button[name='remove_o']").click(function() {
				var o_id = $(this).attr('id');
				alert(o_id); // alert(this.id); or alert($(this).attr('id'));
				//return false;
			});
			
			
		});		
		
		

	</script>
	
	
	<script>
		/*
		function check(){

			if( document.myform.newkey.value.length==0){
				document.myform.newkey.focus();
				alert("請填寫關鍵字");
				return false;
			}
			else{
				document.myform.action ="action.php?q=newkey";
			}
		}
		*/
		function checkScore(){
			//alert(document.ScoreForm.my_input.value);

			var score_LB = document.ScoreForm.score[0].checked;
            var score_LM = document.ScoreForm.score[1].checked;
            var score_SG = document.ScoreForm.score[2].checked;
            var score_RB = document.ScoreForm.score[3].checked;
            var score_RM = document.ScoreForm.score[4].checked;
            var score_SB = document.ScoreForm.score[5].checked;

            //alert(score_LB+"_"+score_LM+"_"+score_SG+"_"+score_RB+"_"+score_RM+"_"+score_SB);return false;

			var msg = "";
			var state = 1;
			if( score_LB == false && score_LM == false && score_SG == false && score_RB == false && score_RM == false && score_SB == false ){
				state = 0;
				msg = msg + "Please choose score.\n";
			}
			
			if( state == 0 ){
				alert(msg);
				return false;
			}
			else{
				return true;
            }
		}
		
		function Change_c(i){
			//alert(c5);
			//eval('alert(c'+i+');');//more_ci little_ci
			eval('var l_contentId = document.getElementById("little_c'+i+'");');
			eval('var m_contentId = document.getElementById("more_c'+i+'");');
			//alert(contentId);
			l_contentId.style.display == "block" ? l_contentId.style.display = "none" : l_contentId.style.display = "block";
			m_contentId.style.display == "block" ? m_contentId.style.display = "none" : m_contentId.style.display = "block";
		
		}
		
		function Change_sc(i){
			//alert(c5);
			//eval('alert(c'+i+');');//more_ci little_ci
			eval('var l_contentId = document.getElementById("little_sc'+i+'");');
			eval('var m_contentId = document.getElementById("more_sc'+i+'");');
			l_contentId.style.display == "block" ? l_contentId.style.display = "none" : l_contentId.style.display = "block";
			m_contentId.style.display == "block" ? m_contentId.style.display = "none" : m_contentId.style.display = "block";
		
		}


	</script>

</head>
<body>
	<div id="mainNavbar" class="navbar navbar-inverse navbar-static-top">
	<div class="navbar-inner">
		<div class="container">
			<div class="btn-group pull-right">
            <?php if( !isset($_SESSION['uname']) ){?>
            <a id="mainNav_loginBtn" class="btn" href="index.php">Local Kernal</a>
            <a id="mainNav_loginBtn" class="btn disabled" href="index_web.php">Web Kernal</a>
			<a id="mainNav_loginBtn" class="btn" href="login.php">登入</a>
			<?php }else if( isset($_SESSION['uadmin']) ){?>
			<a id="mainNav_loginBtn" class="btn disabled" href=""><?php echo $_SESSION['uname'];?></a>
            <a id="mainNav_loginBtn" class="btn" href="index.php">Local Kernal</a>
            <a id="mainNav_loginBtn" class="btn disabled" href="index_web.php">Web Kernal</a>
			<a id="mainNav_loginBtn" class="btn" href="index_list.php">Save Results</a>
            <a id="mainNav_loginBtn" class="btn" href="allScore.php">分數統計</a>
			<a id="mainNav_loginBtn" class="btn" href="logout.php">登出</a>
			<?php }else{?>
			<a id="mainNav_loginBtn" class="btn disabled" href=""><?php echo $_SESSION['uname'];?></a>
            <a id="mainNav_loginBtn" class="btn" href="index.php">Local Kernal</a>
            <a id="mainNav_loginBtn" class="btn disabled" href="index_web.php">Web Kernal</a>
			<a id="mainNav_loginBtn" class="btn" href="index_list.php">Save Results</a>
            <a id="mainNav_loginBtn" class="btn" href="logout.php">登出</a>
			<?php }?>		
			</div>
		</div>
	</div>
	</div>
	
	<!-- 1A block start --------------------------------------------------------------------------------------------------------------------------------->
	<p></p>
	
	<div class="row-fluid">
	<div class="container">
		<p></p>
		<div class="span1"></div>
		<form class="form-horizontal" id="SearchForm" method="post" action="index_web.php">
		<div class="input-append">
			<input class="span10" name="keywordtext" type="text" value="<?php echo $key; ?>">
			<button id="filterDataBtn" class="btn" type="submit">Search</button>
			<button id="OptionBtn" class="btn" type="button">Options</button>
			<?php if( isset($_SESSION['uname']) ){?>
			<button id="AddKeyBtn" class="btn" type="button">AddKey</button>
			<button id="SaveBtn" class="btn" type="button">Save</button>
			<?php }?>
		</div>
		
		<div id="info_sf" class="alert alert-block" style="display:none;width:80%;margin-left:8%;">
			<div class="control-group" style="margin-bottom:0px;">
			<label class="control-label"><font size="4" color="black"><b>Kernal</b></font></label>
			<div class="controls">
				<label class="radio inline"><input type="radio" name="select_kernal" value="Google"> Google </label>
				<label class="radio inline"><input type="radio" name="select_kernal" value="Yahoo" > Yahoo </label>
				<label class="radio inline"><input type="radio" name="select_kernal" value="Both" checked> Both(default) </label>
			</div>
			</div>
			<div class="control-group" style="margin-bottom:0px;">
			<label class="control-label"><font size="4" color="black"><b>Types</b></font></label>
			<div class="controls">
				<label class="radio inline"><input type="radio" name="select_types" value="web" checked> Web(default) </label>
				<label class="radio inline"><input type="radio" name="select_types" value="images"> Images </label>
				<label class="radio inline"><input type="radio" name="select_types" value="news"> News </label>
				<label class="radio inline"><input type="radio" name="select_types" value="video"> Video </label>
			</div>
			</div>
			<div class="control-group" style="margin-bottom:0px;">
			<label class="control-label"><font size="4" color="black"><b>Sort</b></font></label>
			<div class="controls">
				<label class="radio inline"><input type="radio" name="select_sort" value="ASC"> ASC </label>
				<label class="radio inline"><input type="radio" name="select_sort" value="DESC"> DESC </label>
				<label class="radio inline"><input type="radio" name="select_sort" value="Auto" checked> Auto(default) </label>
			</div>
			</div>
			<div class="control-group" style="margin-bottom:0px;">
			<label class="control-label"><font size="4" color="black"><b>Result Size</b></font></label>
			<div class="controls">
				<label class="radio inline"><input type="radio" name="select_size" value="10" checked> 10(default) </label>
				<label class="radio inline"><input type="radio" name="select_size" value="20"> 20 </label>
				<label class="radio inline"><input type="radio" name="select_size" value="40"> 40 </label>
				<label class="radio inline"><input type="radio" name="select_size" value="100"> 100 </label>
			</div>
			</div>
		</div>
		<div class="bs-docs-example"><ul class="nav nav-pills"><?php echo getkeys(); ?></ul></div>
		</form>
		<?php if( $kernal == "Both"){ ?>
		<form method="post" action="action.php?ac=Save" id="SaveForm">
		<input class="span10" name="keywordtext" type="hidden" value="<?php echo $key; ?>">
		<input class="span10" name="kernal1" type="hidden" value="<?php echo $left; ?>">
		<input class="span10" name="kernal2" type="hidden" value="<?php echo $right; ?>">
		<div class="container-fluid">
			<div class="row-fluid">
				<div class="row grid span6">
					<?php echo $left_content;?>
				</div>
				<div class="row grid span6">
					<?php echo $right_content;?>
				</div>
			</div>
		</div>
		</form>
		<?php }
			  else if( $kernal == "Google" || $kernal == "Yahoo" ){ ?>
		<form method="post" action="action.php?ac=Save" id="SaveForm">
		<input class="span10" name="keywordtext" type="hidden" value="<?php echo $key; ?>">
		<div class="container-fluid">
			<div class="row-fluid">
				<div class="row grid span12">
					<?php 
						if( $kernal == "Google" ){
							echo "<div class='alert alert-success'>Google</div>".k_google(1,0);
						}
						else if( $kernal == "Yahoo" ){
							echo "<div class='alert alert-info'>Yahoo</div>".k_yahoo(0,1);
						}
					?>
				</div>
			</div>
		</div>
		</form>
		<?php }if( isset($_SESSION['uname']) ){ ?>
		<!-- Score Div -->
		<?php if( $kernal == "Both"){?>
		<div class="alert alert-block">
			<div class="row-fluid">
				<div class="span12">
					
					<form name="ScoreForm" action="action.php?ac=Score" method="post" onsubmit="return checkScore();">
						<table border="0">
							<tr>
								<td>評分：</td>
							</tr>
							<tr>
								<td align="center">
                                    <label class="radio inline"><input type="radio" name="score" value="<?php echo $left." 5";?>"> Left Best</label>
                                    <label class="radio inline"><input type="radio" name="score" value="<?php echo $left." 3";?>"> Left Middle</label>
                                    <label class="radio inline"><input type="radio" name="score" value="<?php echo "Same 5";?>"> The Same Good</label>
                                    <label class="radio inline"><input type="radio" name="score" value="<?php echo $right." 3";?>"> Right Middle</label>
                                    <label class="radio inline"><input type="radio" name="score" value="<?php echo $right." 5";?>"> Right Best</label>
                                    <label class="radio inline"><input type="radio" name="score" value="<?php echo "Same 0";?>"> The Same Bad</label>
                                    <input type="hidden" name="key" value="<?php echo $key;?>"/>
									<input type="hidden" name="kernal" value="<?php echo $kernal;?>"/>
								</td>
							</tr>
							<tr>
								<td align="center">
									<button id="ResetBtn" class="btn" type="reset">Reset</button>
									<button id="SureScoreBtn" class="btn" type="submit">Submit</button>
								</td>
							</tr>
					</form>
					
				</div>
			</div>
		</div>
		
		<?php }else if( $kernal == "Google" || $kernal == "Yahoo" ){ ?>
		<div class="alert alert-block">
			<div class="row-fluid">
				<div class="span12">

					<form name="ScoreForm" action="action.php?ac=Score" method="post">
						<table border="0">
							<tr>
								<td>評分：</td>
							</tr>
							<tr>
								<td>
									<input name="my_input" value="3" id="rating_star" type="hidden">
									<input type="hidden" name="key" value="<?php echo $key;?>"/>
									<input type="hidden" name="kernal" value="<?php echo $kernal;?>"/>
								</td>
							</tr>							
							<tr>
								<td align="center">
									<button id="SureScoreBtn" class="btn" type="submit">Submit</button>
								</td>
							</tr>
					</form>
					
				</div>
			</div>
		</div>
		<?php }} ?>			
	</div>
	</div>
	
	<!-- 1A block end ---------------------------------------------------------------------------------------------------------------------------------------->
	
	
</body>
</html>
