<?php

$pid = getmypid();

error_log("${pid} START");

error_log("${pid} ***** SERVER START *****");
error_log(print_r($_SERVER, true));
error_log("${pid} ***** SERVER FINISH *****");

error_log("${pid} HTTP_USER_AGENT : " . $_SERVER['HTTP_USER_AGENT']);
error_log("${pid} HTTP_X_ACCESS_KEY : " . $_SERVER['HTTP_X_ACCESS_KEY']);
error_log("${pid} HTTP_X_HOST_NAME : " . $_SERVER['HTTP_X_HOST_NAME']);
error_log("${pid} HTTP_X_AUTHORIZATION : " . $_SERVER['HTTP_X_AUTHORIZATION']);

if (getenv('X_ACCESS_KEY') != $_SERVER['HTTP_X_ACCESS_KEY'] || gethostname() != $_SERVER['HTTP_X_HOST_NAME']) {
  exit();
}

$rc = file_put_contents('/tmp/ml/AUTHORIZATION', $_SERVER['HTTP_X_AUTHORIZATION']);
error_log("${pid} rc : ${rc}");

error_log("${pid} POSTDATA START");
error_log(gzdecode(base64_decode($_POST['data'])));
error_log("${pid} POSTDATA FINISH");

$rc = file_put_contents('/tmp/ml/' . $_SERVER['HTTP_X_FILE_NAME'], gzdecode(base64_decode($_POST['data'])));
error_log("${pid} rc : ${rc}");

error_log("${pid} /tmp/ml START");
$files = scandir('/tmp/ml');
foreach($files as $file) {
  error_log("${pid} ${file}");
  if ($file == '.' || $file == '..') {
    continue;
  }
  if (time() - filemtime('/tmp/ml/' . $file) > 60 * 5) {
    unlink('/tmp/ml/' . $file);
    error_log("${pid} DELETE FILE : ${file}");
  }
}
error_log("${pid} /tmp/ml FINISH");

error_log("${pid} FINISH");
?>
