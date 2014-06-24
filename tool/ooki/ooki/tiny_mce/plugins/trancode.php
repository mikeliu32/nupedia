<?
$from = $_GET["from"];
$to = $_GET["to"];
$term = $_GET["term"];
header('Content-type:text/html; charset=utf-8');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', FALSE);
header('Pragma: no-cache');

if($form != $to)
{
	$term = iconv($from, $to, $term);
}
$term = urlencode($term);
echo $term;
?>
