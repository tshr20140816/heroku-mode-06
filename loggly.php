<?php

$type = $argv[1]; // 'A' or 'E'
$prefix = $argv[2];

$stdin = fopen('php://stdin', 'r');
ob_implicit_flush(true);

while ($line = fgets($stdin)) {
  if ($type == 'A') {
    $array = explode(' ', $line, 3);
    $servername = $array[1];
    file_put_contents('/app/SERVER_NAME', $servername);
    
    if (file_exists('/app/HOME_IP_ADDRESS')) {
      $home_ip_address = file_get_contents('/app/HOME_IP_ADDRESS');
      unlink('/app/HOME_IP_ADDRESS');
      $url = 'https://logs-01.loggly.com/inputs/' . getenv('LOGGLY_TOKEN') . '/tag/' . $servername . '/';
      $context = array(
        "http" => array(
          "method" => "POST",
          "header" => array(
            "Content-Type: text/plain"
          ),
        "content" => $prefix . ' ' . $line
        ));
      $res = file_get_contents($url, false, stream_context_create($context));
    }
  } else {
    $servername = 'Unknown';
    if (file_exists('/app/SERVER_NAME')) {
      $servername = file_get_contents('/app/SERVER_NAME');
    }
    $line = "${servername} ${line}";
  }
  
  $url = 'https://logs-01.loggly.com/inputs/' . getenv('LOGGLY_TOKEN') . '/tag/' . $servername . '/';
  
  $context = array(
    "http" => array(
      "method" => "POST",
      "header" => array(
        "Content-Type: text/plain"
      ),
      "content" => $prefix . ' ' . $line
    ));
  $res = file_get_contents($url, false, stream_context_create($context));
}

?>
