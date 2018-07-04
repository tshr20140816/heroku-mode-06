<?php

$pid = getmypid();

error_log("${pid} START");

error_log("${pid} " . $_SERVER['HTTP_USER_AGENT']);
error_log("${pid} " . $_SERVER['HTTP_X_ACCESS_KEY']);
error_log("${pid} " . $_SERVER['HTTP_X_HOST_NAME']);

if (getenv('X_ACCESS_KEY') != $_SERVER['HTTP_X_ACCESS_KEY']) {
  exit();
}

@mkdir('/tmp/ml');
// file_put_contents('/tmp/ml/' . $_SERVER['HTTP_X_FILE_NAME'], $_POST);

error_log("${pid} POSTDATA START");
error_log(base64_decode($_POST['data']));
error_log("${pid} POSTDATA FINISH");

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
