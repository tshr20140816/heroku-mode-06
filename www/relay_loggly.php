<?php

$pid = getmypid();

if (!isset($_POST['tag']) || $_POST['tag'] === '') {
  error_log("${pid} tag is None.");
  exit();
}

if (!isset($_POST['message']) || $_POST['message'] === '') {
  error_log("${pid} message is None.");
  exit();
}

$message = $_POST['message'];
$country_name = '';

error_log("${pid} ${message}");

if (preg_match('/ \d+\.\d+\.\d+\.\d+ /', $message, $matches) === 1) {
  $ip_address = trim($matches[0]);
  if (file_exists("/tmp/${ip_address}")) {
    $country_name = file_get_contents("/tmp/${ip_address}");
  } else {
    $url = 'http://freegeoip.net/json/' . $ip_address;
    $json = json_decode(file_get_contents($url), true);
    $country_name = $json['country_name'];
    file_put_contents("/tmp/${ip_address}", $json['country_name']);
  }  
}

error_log("${pid} ${country_name}");

$url = 'https://logs-01.loggly.com/inputs/' . getenv('LOGGLY_TOKEN') . '/tag/' . $_POST['tag'] . ',Attack/';

$context = [
  'http' => [
    'method' => 'POST',
    'header' => [
      'Content-Type: text/plain'
    ],
    'content' => "${message} [ ${country_name} ]"
  ]];
$res = file_get_contents($url, false, stream_context_create($context));

error_log("${pid} ${res}");

if ($country_name != 'Japan') {
  return;
}

$res = file_get_contents("http://ipaddress.is/${ip_address}");
$pos = strpos($res, '<b>IP Whois Information');
$res = substr($res, $pos);
$pos = strpos($res, '</tbody>');
$res = preg_replace('/<.+?>/', "\n", substr($res, 0, $pos));
$res = preg_replace('/(\n|\r|\r\n)+/us',"\n",$res);

$context = [
  'http' => [
    'method' => 'POST',
    'header' => [
      'Content-Type: text/plain'
    ],
    'content' => $res
  ]];
$res = file_get_contents($url, false, stream_context_create($context));

error_log("${pid} ${res}");
?>
