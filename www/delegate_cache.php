<?php

$pid = getmypid();

error_log("${pid} START");

if (getenv('X_ACCESS_KEY') != $_SERVER['HTTP_X_ACCESS_KEY']) {
  exit();
}

@mkdir('/tmp/cache_delegate/');
file_put_contents('/tmp/cache_delegate/' . $_SERVER['HTTP_X_FILE_NAME'], $_POST);

error_log("${pid} FINISH");
?>
