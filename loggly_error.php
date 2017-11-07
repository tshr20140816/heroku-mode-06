<?php
$stdin = fopen('php://stdin', 'r');
ob_implicit_flush(true);
while ($line = fgets($stdin))
{
  //$array = explode(' ', $line, 3);
  //$servername = $array[1];
  //$url = 'https://logs-01.loggly.com/inputs/' . getenv('LOGGLY_TOKEN') . '/tag/' . $servername . '/';
  $url = 'https://logs-01.loggly.com/inputs/' . getenv('LOGGLY_TOKEN') . '/tag/Error/';
  
  $context = array(
  "http" => array(
    "method" => "POST",
    "header" => array(
      "Content-Type: text/plain"
      ),
    "content" => 'E ' . $line
    )
  );
  $res = file_get_contents($url, false, stream_context_create($context));
}
?>
