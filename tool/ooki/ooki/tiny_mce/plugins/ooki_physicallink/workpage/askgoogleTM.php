<?
$term = $_GET["term"];
$cset = $_GET["cset"];

if($cset == "UTF-8")
{
        header('Content-type:text/html; charset=utf-8');
}
else if($cset == "BIG5")
{
        header('Content-type:text/html; charset=big5');
}

$serp = file_get_contents("http://www.google.com.tw/search?q=".urlencode($term));

$rule = "/<h3 class=\"?r\"?><a href=\"(.*)\".*>(.*)<\\/a>/Ui";
$counter = 0;
$res = null;
while(preg_match($rule, $serp, $match) == 1)
{
	$serp = substr($serp, strpos($serp, $match[0]) + strlen($match[0]));
	$res[$counter][0] = $match[1];
	$res[$counter][1] = strip_tags(iconv("big5", $cset, $match[2]));
	$counter++;
}

echo $counter . "TO_SEPARATE_TAG_TM";
for($i = 0;$i < $counter;$i++)
{
	echo $res[$i][0] . "TO_SEPARATE_TAG_TM";
	echo $res[$i][1] . "TO_SEPARATE_TAG_TM";
}
?>
