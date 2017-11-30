<?php

$stdin = fopen('php://stdin', 'r');
ob_implicit_flush(true);

while ($line = fgets($stdin)) {
 
  $url = 'https://logs-01.loggly.com/inputs/' . getenv('LOGGLY_TOKEN') . "/tag/${server_name}/";
  
  $context = array(
    'http' => array(
      'method' => 'POST',
      'header' => array(
        'Content-Type: text/plain'
      ),
      'content' => "DELEGATE ${line}"
    ));
  $res = file_get_contents($url, false, stream_context_create($context));
}

?>
