<?php

$asin = $_GET['a'];

error_log($asin);

$url='https://www.amazon.co.jp/dp/' . $asin;

$options = [
  'http' => [
    'method' => 'GET',
    'header' => 'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:56.0) Gecko/20100101 Firefox/59.0',
  ],
];
$context = stream_context_create($options);

$html = file_get_contents($url, false, $context);

$rc = preg_match('/<title>(.+?)<\/title>/', $html, $matches);
error_log($matches[1]);
$title = $matches[1];

$rc = preg_match('/data-asin-price="(.+?)"/', $html, $matches);
error_log($matches[1]);
$price = $matches[1];

echo 'END';
