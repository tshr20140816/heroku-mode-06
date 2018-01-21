<?php

if (!isset($_POST['tag']) || $_POST['tag'] === '') {
  error_log('tag is None.');
  exit();
}

if (!isset($_POST['content']) || $_POST['content'] === '') {
  error_log('content is None.');
  exit();
}

$content = $_POST['content'];
$country_name = '';

error_log($content);

if (preg_match('/ \d+\.\d+\.\d+\.\d+ /', $content, $matches) === 1) {
  $url = 'http://freegeoip.net/json/' . trim($matches[0]);
  $json = json_decode(file_get_contents($url), true);
  $country_name = $json['country_name'];
}

error_log($country_name);

$url = 'https://logs-01.loggly.com/inputs/' . getenv('LOGGLY_TOKEN') . '/tag/' . $_POST['tag'] . '/';

$context = [
  'http' => [
    'method' => 'POST',
    'header' => array(
      'Content-Type: text/plain'
    ),
    'content' => $content . ' ' . $country_name
  ]];
$res = file_get_contents($url, false, stream_context_create($context));

error_log($res);
?>
