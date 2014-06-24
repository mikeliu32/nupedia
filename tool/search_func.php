<?php

if(isset($_GET['query']) && isset($_GET['type'])){

	$type = $_GET['type'];

	$queryStr = urldecode($_GET['query']);

	//echo $queryStr;
	
	switch($type){

		case 'w':
			$results = gsearch_website($queryStr, 30);
			break;

		case 'n':
			$results = gsearch_news($queryStr,50);
			break;

		case 'i':
			$results = gsearch_image($queryStr,50);
			break;

		case 'p':
			$results = nupediaSearch($queryStr);
			break;
	}

	echo json_encode($results);

	//$results = gsearch_website($queryStr,10);
	
	//print_r($results);
}


//Google Search Parsing
function gsearch_website($qStr, $size){

    $gsearch_url = "http://www.google.com.tw/search?q=".urlencode($qStr)."&num=".$size."&oe=utf-8";  //size設定回傳結果筆數   oe設定回傳結果編碼

    $curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $gsearch_url);
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);

    $html_result = curl_exec($curl);
    curl_close($curl);
	
	//先用<li class="g">切割成一筆一筆放入array
    $gsearch_result_row = my_reg_split($html_result,'<li class="g">');

	$gsearch_results=array();
	
	$gsearch_result_ct=0;
	
	for($i=1;$i<count($gsearch_result_row);$i++){

		preg_match("/<h3 class=\"r\"><a href=\"([^\"]*)\"[^>]*>([^<]*)<\/a>/", $gsearch_result_row[$i], $preg_matches);
		$result_row_title = $preg_matches[2];  //取得title
		
		if( !(preg_match('/<a href=\"\/search\?q=/', $gsearch_result_row[$i], $matches_search)) && !(preg_match('/<a href=\"\/images\?q=/', $gsearch_result_row[$i], $matches_search)) ){
			//上面 if 過濾掉新聞搜尋和圖片搜尋結果
			//print_r($matches);
			
				preg_match("/<cite>(.*)<\/cite>/", $gsearch_result_row[$i], $matches_url);
				//$result_row_url = @ereg_replace ('\/url\?q=','http://www.google.com/url?q=', $matches_url[1]); //取得連結  修改連結網址  
				$result_row_url= $matches_url[1];
				//因為原本抓到的連結是無法使用的
				//echo $o_link."<br/>";
				preg_match("/<span class=\"st\">(.*?[^<]+)/", $gsearch_result_row[$i], $matches_content);
				$result_row_desc = @ereg_replace ('<','', $matches_content[1]);	//取得內容描述  因為regular會多一個<  所以要取代掉	
			
			
			//echo $result_row_title." ".$result_row_url." ".$result_row_desc."<br>";

			$result = array();
			$result['title'] = $result_row_title;
			$result['link'] = "http://".$result_row_url;
			$result['desc'] = $result_row_desc;
			
			$gsearch_results[] = $result;
			
			$gsearch_result_ct++;		
		}

	}


	
	return $gsearch_results;
}



//Google Search Parsing
function gsearch_news($qStr, $size){

    $gsearch_url = "http://www.google.com.tw/search?tbm=nws&q=".urlencode($qStr)."&num=".$size."&oe=utf-8";  //size設定回傳結果筆數   oe設定回傳結果編碼

    $curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $gsearch_url);
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);

    $html_result = curl_exec($curl);
    curl_close($curl);
	
	//先用<li class="g">切割成一筆一筆放入array
    $gsearch_result_row = my_reg_split($html_result,'<li class="g">');

	$gsearch_results=array();
	
	$gsearch_result_ct=0;
	
	for($i=1;$i<count($gsearch_result_row);$i++){
		//echo $gsearch_result_row[$i];
		preg_match("/<h3 class=\"r\"><a href=\"([^\"]*)\"[^>]*>([^<]*)<\/a>/", $gsearch_result_row[$i], $preg_matches);
		$result_row_title = $preg_matches[2];  //取得title
		
			//上面 if 過濾掉新聞搜尋和圖片搜尋結果
			//print_r($matches);
			
				//preg_match("/<cite>(.*)<\/cite>/", $gsearch_result_row[$i], $matches_url);
				//preg_match("/q=(.*)&sa=/", $preg_matches[1], $matches_url); //取得連結  修改連結網址
				
			$result_row_url= substr($preg_matches[1], 7, strpos($preg_matches[1], "&")-7);

			preg_match("/<div class=\"slp\">\s*<span class=\"f\">([^<]*)<\/span>/", $gsearch_result_row[$i], $matches_src);
			$result_row_src = $matches_src[1];	//取得內容描述  因為regular會多一個<  所以要取代掉	
			
			preg_match("/<div class=\"st\">([^<]*)<\/div>/", $gsearch_result_row[$i], $matches_desc);
			$result_row_desc = $matches_desc[1];	//取得內容描述  因為regular會多一個<  所以要取代掉	

			
			
			//echo $result_row_title." ".$result_row_url." ".$result_row_desc."<br>";

			$result = array();
			$result['title'] = $result_row_title;
			$result['link'] = urldecode($result_row_url);
			$result['src'] = $result_row_src;
			$result['desc'] = $result_row_desc;

			
			$gsearch_results[] = $result;
			
			$gsearch_result_ct++;		
		

	}


	
	return $gsearch_results;
}

//Google Search Parsing
function gsearch_image($qStr, $size){

    $gsearch_url = "http://www.google.com.tw/search?tbm=isch&q=".urlencode($qStr)."&oe=utf-8";  //size設定回傳結果筆數   oe設定回傳結果編碼

    $curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $gsearch_url);
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);

    $html_result = curl_exec($curl);
    curl_close($curl);

    $gsearch_result_row = my_reg_split($html_result,'<td style="width:25%;');
	$gsearch_results=array();
	
	$gsearch_result_ct=0; 
	
	for($i=1;$i<count($gsearch_result_row);$i++){
		preg_match("/<img height=\"([0-9]+)\" src=\"([^\"]*)\" width=\"([0-9]+)\">/", $gsearch_result_row[$i], $preg_matches);
		$result_row_url = $preg_matches[2];  //取得title
		$result_row_height=$preg_matches[1];
		$result_row_width=$preg_matches[3];

			$result = array();
			$result['link'] = $result_row_url;
			$result['height'] = $result_row_height;
			$result['width'] = $result_row_width;
			
			$gsearch_results[] = $result;
			
			$gsearch_result_ct++;		
	}
	
	return $gsearch_results;
}


//Google Search Parsing
function nupediaSearch($qStr){

$elasticUrl_search = "http://gaisq.cs.ccu.edu.tw:9200/nupedia/entry/_search?q=";

$searchUrl = $elasticUrl_search.urlencode($qStr);

$ch = curl_init($searchUrl);

curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);

curl_close($ch);  // Seems like good practice

$resultJObj = json_decode($result);

$resultList = $resultJObj->hits->hits;
$npsearch_results = array();
	foreach($resultList as $sr){

		$entry = $sr->_source;
	
		$imagePath = ($entry->image)? "../npdata/".$entry->author."/".$entry->eid."/images/".$entry->image : "defaultPic.png";
		$result = array();
		$result['link'] = "index.php?site=".$entry->author."/".$entry->eid;
		$result['title'] = $entry->title;
		$result['image'] = $imagePath;
		$result['author'] = $entry->author;
		$result['abstract'] = mb_strimwidth($entry->abstract_plain, 0, 300, "...", "UTF-8");
		
		$npsearch_results[] = $result;
			
	}
	
	return $npsearch_results;
}

function my_reg_split($s_content,$s_pattern){
	
	$start = strpos ($s_content, $s_pattern);
 
	$s_len = mb_strlen($s_content,"utf-8");
	
	$i = 1;
	$pat_addr[0] = $start;
	
	while($start != $s_len){
		
		$start = $start+20;
		
		$sub_content = substr($s_content,$start,$s_len);
		$s_pat = strpos ($sub_content, $s_pattern);
		if( $s_pat != false){
			$addr = $start+$s_pat;
			$pat_addr[$i] = $addr;
		
			$start = $addr;
			$i++;
		}
		else{
			$start = $s_len;
		}
	
	}
	
	$max = count($pat_addr);
	
	for($i=0;$i<=$max;$i++){
		if($i==0){
			//echo "From 0 To ".$pat_addr[$i]."<br/>";
			$len = 0;
			$len = $pat_addr[$i]-0;
			$o_result = substr($s_content,0,$len);
			$o_result = @ereg_replace ('</b>','', $o_result);
			$o_result = @ereg_replace ('<br>','', $o_result);
			$s_result[$i] = @ereg_replace ('<b>','', $o_result);
		}
		else if($i==$max){
			//echo "From ".$pat_addr[$i-1]." To ".$s_len."<br/>";
			$len = 0;
			$len = $s_len-$pat_addr[$i-1];
			$o_result = substr($s_content,$pat_addr[$i-1],$len);
			$o_result = @ereg_replace ('</b>','', $o_result);
			$o_result = @ereg_replace ('<br>','', $o_result);
			$s_result[$i] = @ereg_replace ('<b>','', $o_result);
		}
		else{
			//echo "From ".$pat_addr[$i-1]." To ".$pat_addr[$i]."<br/>";
			$len = 0;
			$len = $pat_addr[$i]-$pat_addr[$i-1];
			$o_result = substr($s_content,$pat_addr[$i-1],$len);
			$o_result = @ereg_replace ('</b>','', $o_result);
			$o_result = @ereg_replace ('<br>','', $o_result);
			$s_result[$i] = @ereg_replace ('<b>','', $o_result);
		}
	}
	
	return $s_result;
}
?>
