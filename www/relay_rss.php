<?php

$if_modified_since = $_SERVER['HTTP_IF_MODIFIED_SINCE'];
$url = url_decode($_GET['u']);

$ch = curl_init($url);

if ($if_modified_since != NULL) {
  curl_setopt($ch, CURLOPT_HTTPHEADER, "If-Modified-Since: $if_modified_since");
}
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
curl_setopt($ch, CURLOPT_ENCODING, "");

?>
