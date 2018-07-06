<?php
$pid = getmypid();

$uri = $_SERVER['REQUEST_URI'];

error_log("${pid} ***** START ***** ${uri}");

error_log("${pid} HTTP_AUTHORIZATION : " . $_SERVER['HTTP_AUTHORIZATION']);
error_log("${pid} RANGE : " . $_GET['range']);

switch (true) {
  case !isset($_SERVER['HTTP_AUTHORIZATION'], $_GET['range']):
  case !file_exists('/tmp/ml/AUTHORIZATION'):
  case !file_exists('/tmp/ml/' . $_GET['range']):
  case $_SERVER['HTTP_AUTHORIZATION'] != file_get_contents('/tmp/ml/AUTHORIZATION'):
    header('Content-Type: text/plain');
    echo 'DUMMY';
    error_log("${pid} ***** FINISH ***** ${uri}");
    exit();
}

header('Content-Type: text/html; charset=iso-2022-jp');
  
echo file_get_contents('/tmp/ml/' . $_GET['range']);

error_log("${pid} ***** FINISH ***** ${uri}");
?>
