<?php

$url = url_decode($_GET['u']);

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
curl_setopt($ch, CURLOPT_ENCODING, "");

$contents = curl_exec($ch);

$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

error_log($http_code);
error_log($contents);

header('Content-Encoding: gzip');
header('Content-Type: application/xml');
  
echo gzencode($contents, 9);

?>
