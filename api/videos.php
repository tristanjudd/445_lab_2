<?php
header('Access-Control-Allow-Origin: *');

$string = $_SERVER['REQUEST_URI'];
$prefix = "videos.php";
$index = strpos($string, $prefix) + strlen($prefix);
$rq = substr($string, $index);
$file = dirname(__FILE__) . '/videos' . $rq;


header('Content-Type: ' . mime_content_type($file));
header('Content-Disposition: inline; filename="' . basename($file) . '"');
readfile($file);
?>