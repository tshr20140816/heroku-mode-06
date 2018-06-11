<?php

$pid = getmypid();

error_log("${pid} START");

if (!isset($_GET['u']) || $_GET['u'] === '' || is_array($_GET['u'])) {
  error_log("${pid} FINISH 000");
  exit();
}

$url = urldecode($_GET['u']);
error_log("${pid} URL : ${url}");

list($contents, $http_code, $timestamp, $content_type) = get_contents($url, FALSE);

// error_log($http_code);
// error_log($contents);

$cache_file_name = '/tmp/' . urlencode($url);

if ($http_code == '304') {
  header('HTTP/1.1 304 Not Modified');
  if (file_exists($cache_file_name) == FALSE) {
    error_log("${pid} NO CACHE");
    list($contents, $http_code, $timestamp, $content_type) = get_contents($url, TRUE);
    file_put_contents($cache_file_name, $contents);
  }
  loggly_log("O304 R304 ${url}");
  error_log("${pid} FINISH 010");
  exit();
} else if ($http_code == '0') {
  header('HTTP/1.1 500 Warn');
  loggly_log("O- R500 ${url}");
  error_log("${pid} FINISH 020");
  exit();
} else if ($http_code != '200') {
  header('HTTP/1.1 ' . $http_code . ' Warn');
  loggly_log("O${http_code} R${http_code} ${url}");
  error_log("${pid} FINISH 030");
  exit();
} else if (strlen($contents) == 0 ) {
  header('HTTP/1.1 404 File Not Found');
  loggly_log("O200 R404 ${url}");
  error_log("${pid} FINISH 040");
  exit();
}

if (file_exists($cache_file_name)) {
  $cache_contents = file_get_contents($cache_file_name);
  if (($cache_contents == $contents) ||
     (preg_replace('/<pubDate>.+?<\/pubDate>/', '', $cache_contents, 1) ==
     preg_replace('/<pubDate>.+?<\/pubDate>/', '', $contents, 1)) ||
     (preg_replace('/<updated>.+?<\/updated>/', '', $cache_contents, 1) ==
     preg_replace('/<updated>.+?<\/updated>/', '', $contents, 1)) ||
     (preg_replace('/<lastBuildDate>.+?<\/lastBuildDate>/', '', $cache_contents, 1) ==
     preg_replace('/<lastBuildDate>.+?<\/lastBuildDate>/', '', $contents, 1))) {
    header('HTTP/1.1 304 Not Modified');
    loggly_log("O200 R304 ${url}");
    error_log("${pid} FINISH 050");
    exit();
  }
}

file_put_contents($cache_file_name, $contents);

$contents_gzip = gzencode($contents, 9);

if (is_null($content_type)) {
  header('Content-Type: application/xml');
} else {
  header('Content-Type: ' . $content_type);
  error_log("${pid} ORIGINAL Content-Type: " . $content_type);
}
if ($timestamp != -1) {
  header('Last-Modified: ' . gmdate("D, d M Y H:i:s \\G\\M\\T\r\n", $timestamp));
  error_log("${pid} Last-Modified: " . gmdate("D, d M Y H:i:s \\G\\M\\T\r\n", $timestamp));
}

if (strlen($contents_gzip) < strlen($contents)) {
  header('Content-Encoding: gzip');
  header('Content-Length: ' . strlen($contents_gzip));
  echo $contents_gzip;
} else {
  header('Content-Length: ' . strlen($contents));
  echo $contents;
}
loggly_log("O200 R200 ${url}");
error_log("${pid} FINISH 060");

exit();

function get_contents($url_, $force_) {
  $pid = getmypid();
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
  $content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

  curl_close($ch);
  
  error_log("${pid} CURLINFO_FILETIME ${timestamp}");
  
  return [$contents, $http_code, $timestamp, $content_type];
}

function loggly_log($message_) {
  $pid = getmypid();
  error_log("${pid} ${message_}");
  
  $url_loggly = 'https://logs-01.loggly.com/inputs/' . getenv('LOGGLY_TOKEN') . '/tag/relay_rss,' . getenv('HEROKU_APP_NAME') . '/';
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
