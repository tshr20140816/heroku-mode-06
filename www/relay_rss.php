<?php

$pid = getmypid();
$requesturi = $_SERVER['REQUEST_URI'];
error_log("${pid} START ${requesturi}");

/*
error_log("${pid} ***** SERVER ENV START *****");
error_log(print_r($_SERVER, true));
error_log("${pid} ***** SERVER ENV FINISH *****");
*/

if (!isset($_GET['u']) || $_GET['u'] === '' || is_array($_GET['u'])) {
  loggly_log("FINISH 000 NO URL");
  exit();
}

$url = urldecode($_GET['u']);
error_log("${pid} URL : ${url}");

if (!filter_var($url, FILTER_VALIDATE_URL) || !preg_match('@^https?+://@i', $url)) {
  loggly_log("FINISH 010 INVALID URL : ${url}");
  exit();
}

list($contents, $http_code, $timestamp, $content_type) = get_contents($url, FALSE);

// error_log($http_code);
// error_log($contents);

@mkdir('/tmp/cache_rss');
$cache_file_name = '/tmp/cache_rss/' . urlencode($url);

if ($http_code == '304') {
  header('HTTP/1.1 304 Not Modified');
  if (file_exists($cache_file_name) == FALSE) {
    error_log("${pid} NO CACHE");
    list($contents, $http_code, $timestamp, $content_type) = get_contents($url, TRUE);
    file_put_contents($cache_file_name, $contents);
  }
  loggly_log("O304 R304.0 ${url}");
  error_log("${pid} FINISH 020");
  exit();
} else if ($http_code == '0') {
  header('HTTP/1.1 500 Warn');
  loggly_log("O- R500 ${url}");
  error_log("${pid} FINISH 030");
  exit();
} else if ($http_code != '200') {
  header("HTTP/1.1 ${http_code} Warn");
  loggly_log("O${http_code} R${http_code} ${url}");
  error_log("${pid} FINISH 040");
  exit();
} else if (strlen($contents) == 0 ) {
  header('HTTP/1.1 404 File Not Found');
  loggly_log("O200 R404 ${url}");
  error_log("${pid} FINISH 050");
  exit();
}

$no_cache = '';
if (file_exists($cache_file_name)) {
  $cache_contents = file_get_contents($cache_file_name);
  $sub_code = 0;
  if ($cache_contents == $contents) {
    $sub_code = 1;
  } else {
    $patterns =
      ['/<pubDate>.+?<\/pubDate>/',
       '/<updated>.+?<\/updated>/',
       '/<lastBuildDate>.+?<\/lastBuildDate>/',
       '/<updated>.+?<\/updated>\R *?<id>.+?<\/id>/s',
      ];
    for ($i = 0; $i < count($patterns); $i++) {
      if (preg_replace($patterns[$i], '', $cache_contents, 1) == preg_replace($patterns[$i], '', $contents, 1)) {
        $sub_code = $i + 2;
        break;
      }
    }
  }
  if ($sub_code > 0) {
    header('HTTP/1.1 304 Not Modified');
    loggly_log("O200 R304.${sub_code} ${url}");
    error_log("${pid} FINISH 060");
    exit();
  }
} else {
  $no_cache = ' NO_CACHE';
}

file_put_contents($cache_file_name, $contents);

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

/*
$contents_gzip = gzencode($contents, 9);

if (strlen($contents_gzip) < strlen($contents)) {
  header('Content-Encoding: gzip');
  header('Content-Length: ' . strlen($contents_gzip));
  echo $contents_gzip;
} else {
  header('Content-Length: ' . strlen($contents));
  echo $contents;
}
*/
echo $contents;

loggly_log("O200 R200 ${url}${no_cache}");
error_log("${pid} FINISH 070");

exit();

function get_contents($url_, $force_) {
  $pid = getmypid();
  $ch = curl_init();
  curl_setopt_array($ch,
                    [CURLOPT_URL => $url_,
                     CURLOPT_RETURNTRANSFER => TRUE,
                     CURLOPT_ENCODING => '',
                     CURLOPT_CONNECTTIMEOUT => 20,
                     CURLOPT_FOLLOWLOCATION => TRUE,
                     CURLOPT_MAXREDIRS => 3,
                     CURLOPT_FILETIME => TRUE,
                     // CURLOPT_TCP_FASTOPEN => TRUE,
                     // CURLOPT_SSL_FALSESTART => TRUE,
                     CURLOPT_PATH_AS_IS => TRUE,
                     CURLOPT_USERAGENT => getenv('USER_AGENT'),
                    ]);
  if ($force_ != TRUE && isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['If-Modified-Since: ' . $_SERVER['HTTP_IF_MODIFIED_SINCE']]);
    error_log($pid . ' If-Modified-Since : ' . $_SERVER['HTTP_IF_MODIFIED_SINCE']);
  }
  // curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);

  $contents = curl_exec($ch);
  // error_log(curl_getinfo($ch, CURLINFO_HEADER_OUT));
  $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  $timestamp = curl_getinfo($ch, CURLINFO_FILETIME);
  $content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
  
  curl_close($ch);
  
  error_log("${pid} CURLINFO_FILETIME ${timestamp}");
  
  if ($http_code == '403') {
    error_log("${pid} RETRY ${url_}");
    $context = stream_context_create(['http' => ['ignore_errors' => true]]);
    $contents = file_get_contents($url_, FALSE, $context);
    error_log("${pid} RETRY LINE 1 : " . $http_response_header[0]);
    if (strpos($http_response_header[0], ' 200 ')) {
      $http_code = '200';
      foreach($http_response_header as $header) {
        if (strpos(strtolower($header), 'content-type')) {
          $content_type = explode(' ', $header, 2)[1];
          break;
        }
      }
    }
  }
  
  return [$contents, $http_code, $timestamp, $content_type];
}

function loggly_log($message_) {
  $pid = getmypid();
  error_log("${pid} ${message_}");
  
  $url_loggly = 'https://logs-01.loggly.com/inputs/' . getenv('LOGGLY_TOKEN') . '/tag/relay_rss,' . getenv('HEROKU_APP_NAME') . '/';
  $ch = curl_init();
  $rc = curl_setopt_array($ch,
                    [CURLOPT_URL => $url_loggly,
                     CURLOPT_RETURNTRANSFER => TRUE,
                     CURLOPT_ENCODING => '',
                     CURLOPT_CONNECTTIMEOUT => 20,
                     CURLOPT_FOLLOWLOCATION => TRUE,
                     CURLOPT_MAXREDIRS => 3,
                     CURLOPT_POST => TRUE,
                     CURLOPT_HTTPHEADER => ['Content-Type: text/plain'],
                     // CURLOPT_SSL_FALSESTART => TRUE,
                     CURLOPT_PATH_AS_IS => TRUE,
                     CURLOPT_POSTFIELDS => $message_,
                    ]);
  // error_log("${pid} CURL_SETOPT_ARRAY RC : ${rc}");
  $rc = curl_exec($ch);
  // error_log("${pid} CURL_EXEC RC : ${rc}");
  curl_close($ch);
}
?>
