<?php

$pid = getmypid();
$uri = $_SERVER['REQUEST_URI'];

error_log("${pid} ***** START ***** ${uri}");

error_log("${pid} HTTP_X_ACCESS_KEY : " . $_SERVER['HTTP_X_ACCESS_KEY']);
error_log("${pid} HTTP_X_HOST_NAME : " . $_SERVER['HTTP_X_HOST_NAME']);
error_log("${pid} HTTP_X_AUTHORIZATION : " . $_SERVER['HTTP_X_AUTHORIZATION']);
error_log("${pid} HTTP_X_FILE_NAME : " . $_SERVER['HTTP_X_FILE_NAME']);

switch (true) {
  case !isset($_SERVER['HTTP_X_ACCESS_KEY'], $_SERVER['HTTP_X_HOST_NAME'], $_SERVER['HTTP_X_AUTHORIZATION'], $_SERVER['HTTP_X_FILE_NAME']):
  case getenv('X_ACCESS_KEY') != $_SERVER['HTTP_X_ACCESS_KEY']:
  case gethostname() != $_SERVER['HTTP_X_HOST_NAME']:
    error_log("${pid} ***** FINISH ***** ${uri}");
    exit();
}

file_put_contents('/tmp/ml/AUTHORIZATION', $_SERVER['HTTP_X_AUTHORIZATION']);
# file_put_contents('/tmp/ml/' . $_SERVER['HTTP_X_FILE_NAME'], gzdecode(base64_decode($_POST['data'])));
file_put_contents('/tmp/ml/' . $_SERVER['HTTP_X_FILE_NAME'], base64_decode($_POST['data']));

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

error_log("${pid} ***** FINISH ***** ${uri}");
?>
