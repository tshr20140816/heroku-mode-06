<?php

$pid = getmypid();

error_log("${pid} START");

error_log("${pid} " . $_SERVER['HTTP_USER_AGENT']);
error_log("${pid} " . $_SERVER['HTTP_X_ACCESS_KEY']);
error_log("${pid} " . $_SERVER['HTTP_X_HOST_NAME']);

if (getenv('X_ACCESS_KEY') != $_SERVER['HTTP_X_ACCESS_KEY']) {
  exit();
}

error_log("${pid} POSTDATA START");
error_log(gzdecode(base64_decode($_POST['data'])));
error_log("${pid} POSTDATA FINISH");

//@mkdir('/tmp/ml');
$rc = file_put_contents('/tmp/ml/' . $_SERVER['HTTP_X_FILE_NAME'], gzdecode(base64_decode($_POST['data'])));
error_log("${pid} rc : ${rc}");
//$rc = file_put_contents('/app/www/ml/' . $_SERVER['HTTP_X_FILE_NAME'], gzdecode(base64_decode($_POST['data'])));
//error_log("${pid} rc : ${rc}");

error_log("${pid} /tmp/ml START");
$files = scandir('/tmp/ml');
foreach($files as $file) {
  error_log("${pid} ${file}");
  if ($file == '.' || $file == '..') {
    continue;
  }
  if (time() - filemtime('/tmp/ml/' . $file) < 60 * 5) {
    unlink('/tmp/ml/' . $file);
  }
}
error_log("${pid} /tmp/ml FINISH");

/*
error_log("${pid} /app/www/ml START");
$files = scandir('/app/www/ml');
foreach($files as $file) {
  error_log("${pid} ${file}");
}
error_log("${pid} /app/www/ml FINISH");

error_log("${pid} FINISH");

error_log("${pid} /app/www/ttrss2 START");
$files = scandir('/app/www/ttrss2');
foreach($files as $file) {
  error_log("${pid} ${file}");
}
error_log("${pid} /app/www/ttrss2 FINISH");
*/
error_log("${pid} FINISH");
?>
