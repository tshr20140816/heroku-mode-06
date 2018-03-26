<?php

$asin = $_GET['a'];

$url='https://www.amazon.co.jp/dp/' . $asin;

$options = [
  'http' => [
    'method' => 'GET',
    'header' => 'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:56.0) Gecko/20100101 Firefox/59.0',
  ],
];
$context = stream_context_create($options);

$html = file_get_contents($url, false, $context);

$rc = preg_match('/data-asin-price="(.+?)"/', $html, $matches1);

error_log($matches1[1]);
