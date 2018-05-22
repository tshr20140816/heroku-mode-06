<?php

$url = url_decode($_GET['u']);

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
curl_setopt($ch, CURLOPT_ENCODING, "");

$rss =  curl_exec($ch);

curl_close($ch);

echo $rss;

?>
