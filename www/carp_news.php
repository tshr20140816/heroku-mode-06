<?php

$url = 'http://www.carp.co.jp/news18/index.html';
$contents = get_contents($url);

$contents = preg_replace('/<(img|link|script|a|span|meta n|meta c|div).+?>/', '', $contents);
$contents = preg_replace('/<\/(script|a|span|div)>/', '', $contents);
$contents = preg_replace('/ class=".+?"/', '', $contents);
$contents = preg_replace('/<h1.+?<\/ul>/s', '', $contents, 1);
$contents = preg_replace('/<!--.*?-->/s', '', $contents);
$contents = preg_replace('/<style.+?<\/style>/s', '', $contents, 1);
$contents = preg_replace('/<noscript.+?<\/noscript>/s', '', $contents, 1);
$contents = preg_replace('/<body.+?>/', '<body>', $contents, 1);
$contents = preg_replace('/^ +/m', '', $contents);
$contents = preg_replace('/^ *\n/m', '', $contents);
$contents = preg_replace('/<title>.+?<\/title>/', '<title>...</title>', $contents);
$contents = str_replace('<body>', '<body><a href="' . $url . '">link</a>', $contents);

@mkdir('/tmp/cache_page');
$cache_file_name = '/tmp/cache_page/' . urlencode($url);
if (file_exists($cache_file_name) === FALSE) {
  file_put_contents($cache_file_name, $contents);
  $contents = str_replace('<title>...</title>', '<title>first</title>', $contents);
  $contents = str_replace('<head>', '<head><meta http-equiv="refresh" content="600">', $contents);
} else {
  $cache_contents = file_get_contents($cache_file_name);
  if ($cache_contents === $contents) {
    $contents = str_replace('<title>...</title>', '<title>' . date('Hi', strtotime('+9 hours')) . '</title>', $contents);
    $contents = str_replace('<head>', '<head><meta http-equiv="refresh" content="600">', $contents);
  } else {
    file_put_contents($cache_file_name, $contents);
    $contents = str_replace('<title>...</title>', '<title>update</title>', $contents);
  }
}
echo $contents;

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
  @curl_setopt($ch, CURLOPT_TCP_FASTOPEN, TRUE);
  $contents = curl_exec($ch);
  // $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);  
  curl_close($ch);
  
  return $contents;
}
?>
