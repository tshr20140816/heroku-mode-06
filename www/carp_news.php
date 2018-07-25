<?php

$url = 'http://www.carp.co.jp/news18/index.html';
$content = get_contents('http://www.carp.co.jp/news18/index.html');

$content = preg_replace('/<(img|link|script|a|span|meta n|meta c|div).+?>/', '', $content);
$content = preg_replace('/<\/(script|a|span|div)>/', '', $content);
$content = preg_replace('/ class=".+?"/', '', $content);
$content = preg_replace('/<h1.+?<\/ul>/s', '', $content, 1);
$content = preg_replace('/<!--.*?-->/s', '', $content);
$content = preg_replace('/<style.+?<\/style>/s', '', $content, 1);
$content = preg_replace('/<noscript.+?<\/noscript>/s', '', $content, 1);
$content = preg_replace('/<body.+?>/', '<body>', $content, 1);
$content = preg_replace('/^ +/m', '', $content);
$content = preg_replace('/^ *\n/m', '', $content);
$content = preg_replace('/<title>.+?<\/title>/', '<title>...</title>', $content);
$content = str_replace('<head>', '<head><meta http-equiv="refresh" content="600">', $content);

@mkdir('/tmp/cache_page');
$cache_file_name = '/tmp/cache_page/' . urlencode($url);
if (file_exists($cache_file_name) === FALSE) {
  file_put_contents($cache_file_name, $contents);
  $content = str_replace('<title>...</title>', '<title>first</title>', $content);
} else {
  $cache_contents = file_get_contents($cache_file_name);
  if ($cache_contents === $contents) {
    $content = str_replace('<title>...</title>', '<title>' . date('Hi') . '</title>', $content);
  } else {
    file_put_contents($cache_file_name, $contents);
    error_log('OLD ' . hash('sha256', $cache_contents));
    error_log($cache_contents);
    error_log('NEW ' . hash('sha256', $content));
    error_log($content);
    $content = str_replace('<title>...</title>', '<title>update</title>', $content);
  }
}
echo $content;

function get_contents($url_) {
  $pid = getmypid();
  $ch = curl_init();
  curl_setopt_array($ch,
                    [CURLOPT_URL => $url_,
                     CURLOPT_RETURNTRANSFER => TRUE,
                     CURLOPT_ENCODING => '',
                     CURLOPT_CONNECTTIMEOUT => 20,
                     CURLOPT_FOLLOWLOCATION => TRUE,
                     CURLOPT_MAXREDIRS => 3,
                     CURLOPT_PATH_AS_IS => TRUE,
                     CURLOPT_USERAGENT => getenv('USER_AGENT'),
                    ]);  
  $contents = curl_exec($ch);
  // $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);  
  curl_close($ch);
  
  return $contents;
}
?>
