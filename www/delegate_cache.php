<?php

$pid = getmypid();

error_log("${pid} START");

if (getenv('X_ACCESS_KEY') != $_SERVER['HTTP_X_ACCESS_KEY']) {
  exit();
}

@mkdir('/tmp/ml');
file_put_contents('/tmp/ml/' . $_SERVER['HTTP_X_FILE_NAME'], $_POST);

$files = scandir('/tmp/ml');
foreach($files as $file) {
  if ($file == '.' || $file == '..') {
    continue;
  }
  if (time() - filemtime('/tmp/ml/' . $file) < 60 * 5) {
    unlink('/tmp/ml/' . $file);
  }
}

error_log("${pid} FINISH");
?>
