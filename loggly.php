<?php

$type = $argv[1]; // 'A' or 'E'
$prefix = $argv[2];

$stdin = fopen('php://stdin', 'r');
ob_implicit_flush(true);

$ch = null;
$count = 0;

while ($line = fgets($stdin)) {
  if ($type == 'E') {
    $line = getenv('HEROKU_APP_NAME') . " ${line}";
  }  
  loggly_log("${prefix} ${line}");
}

exit();

function loggly_log($message_) {
  $url_loggly = 'https://logs-01.loggly.com/inputs/' . getenv('LOGGLY_TOKEN') . '/tag/loggly.php,' . getenv('HEROKU_APP_NAME') . '/';
  
  global $ch, $count;
  if ($count % 10 == 0) {
    $ch = curl_init();
  }
  curl_setopt_array($ch,
                    [CURLOPT_URL => $url_loggly,
                     CURLOPT_RETURNTRANSFER => TRUE,
                     CURLOPT_ENCODING => '',
                     CURLOPT_CONNECTTIMEOUT => 20,
                     CURLOPT_FOLLOWLOCATION => TRUE,
                     CURLOPT_MAXREDIRS => 3,
                     CURLOPT_POST => TRUE,
                     CURLOPT_HTTPHEADER => ['Content-Type: text/plain', 'Connection: Keep-Alive'],
                     CURLOPT_PATH_AS_IS => TRUE,
                     CURLOPT_POSTFIELDS => $message_,
                    ]);
  @curl_setopt($ch, CURLOPT_TCP_FASTOPEN, TRUE);
  curl_exec($ch);
  if ($count % 10 == 9) {
    curl_close($ch);
  }
  $count = ($count + 1) % 10;
}
?>
