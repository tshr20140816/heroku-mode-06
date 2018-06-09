<?php

$pid = getmypid();

error_log("${pid} START");

$url = urldecode($_GET['u']);
error_log("${pid} URL : ${url}");

list($contents, $http_code, $timestamp) = get_contents($url, FALSE);

error_log("${pid} ORIGINAL HTTP STATUS CODE : ${http_code}");
// error_log($contents);

$cache_file_name = '/tmp/' . urlencode($url);

if ($http_code == '304') {
  header('HTTP/1.1 304 Not Modified');
  if (file_exists($cache_file_name) == FALSE) {
    error_log("${pid} NO CACHE");
    list($contents, $http_code, $timestamp) = get_contents($url, TRUE);
    file_put_contents($cache_file_name, $contents);
  }
  error_log("${pid} RETURN HTTP STATUS CODE : 304");
  error_log("${pid} FINISH 010");
  exit();
} else if ($http_code == '0') {
  header('HTTP/1.1 500 Warn');
  error_log("${pid} RETURN HTTP STATUS CODE : 500");
  loggly_log("500 ${url}");
  error_log("${pid} FINISH 020");
  exit();
} else if ($http_code != '200') {
  header('HTTP/1.1 ' . $http_code . ' Warn');
  error_log("${pid} RETURN HTTP STATUS CODE : ${http_code}");
  loggly_log("${http_code} ${url}");
  error_log("${pid} FINISH 030");
  exit();
} else if (strlen($contents) == 0 ) {
  header('HTTP/1.1 404 File Not Found');
  error_log("${pid} RETURN HTTP STATUS CODE : 404");
  loggly_log("404 ${url}");
  error_log("${pid} FINISH 040");
  exit();
}

if (file_exists($cache_file_name)) {
  $cache_contents = file_get_contents($cache_file_name);
  if (($cache_contents == $contents) ||
     (preg_replace('/<pubDate>.+?<\/pubDate>/', '', $cache_contents, 1) ==
     preg_replace('/<pubDate>.+?<\/pubDate>/', '', $contents, 1)) ||
     (preg_replace('/<updated>.+?<\/updated>/', '', $cache_contents, 1) ==
     preg_replace('/<updated>.+?<\/updated>/', '', $contents, 1))) {
    header('HTTP/1.1 304 Not Modified');
    error_log("${pid} RETURN HTTP STATUS CODE : 304");
    error_log("${pid} FINISH 050");
    exit();
  }
}

if ($http_code == '200') {
  file_put_contents($cache_file_name, $contents);
}

$contents_gzip = gzencode($contents, 9);

header('Content-Type: application/xml');
if ($timestamp != -1) {
  header('Last-Modified: ' . gmdate("D, d M Y H:i:s \\G\\M\\T\r\n", $timestamp));
}

if (strlen($contents_gzip) < strlen($contents)) {
  header('Content-Encoding: gzip');
  header('Content-Length: ' . strlen($contents_gzip));
  echo $contents_gzip;
} else {
  header('Content-Length: ' . strlen($contents));
  echo $contents;
}
error_log("${pid} RETURN HTTP STATUS CODE : 200");
loggly_log("200 ${url}");
error_log("${pid} FINISH 060");

function get_contents($url_, $force_) {
  $ch = curl_init();

  curl_setopt($ch, CURLOPT_URL, $url_); 
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
  curl_setopt($ch,  CURLOPT_FILETIME, TRUE);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; rv:56.0) Gecko/20100101 Firefox/60.0'); 
  if ($force_ != TRUE && isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['If-Modified-Since: ' . $_SERVER['HTTP_IF_MODIFIED_SINCE']]);
    error_log($pid . ' If-Modified-Since : ' . $_SERVER['HTTP_IF_MODIFIED_SINCE']);
  }

  $contents = curl_exec($ch);
  $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  $timestamp = curl_getinfo($ch, CURLINFO_FILETIME);

  curl_close($ch);
  
  return [$contents, $http_code, $timestamp];
}

function loggly_log($message_) {
  $url_loggly = 'https://logs-01.loggly.com/inputs/' . getenv('LOGGLY_TOKEN') . '/tag/relay_rss/';
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url_loggly);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
  curl_setopt($ch, CURLOPT_ENCODING, '');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
  curl_setopt($ch, CURLOPT_POST, TRUE);
  curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: text/plain']);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $message_);
  curl_exec($ch);
  curl_close($ch);
}
?>
