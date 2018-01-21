<?php

if (!isset($_POST['tag']) || $_POST['tag'] === '') {
  error_log('tag is None.');
  exit();
}

if (!isset($_POST['message']) || $_POST['message'] === '') {
  error_log('message is None.');
  exit();
}

$message = $_POST['message'];
$country_name = '';

error_log($message);

if (preg_match('/ \d+\.\d+\.\d+\.\d+ /', $message, $matches) === 1) {
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
    'content' => $message . ' ' . $country_name
  ]];
$res = file_get_contents($url, false, stream_context_create($context));

error_log($res);
?>
