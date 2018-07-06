<?php
$pid = getmypid();

error_log("${pid} ***** SERVER START *****");
error_log(print_r($_SERVER, true));
error_log("${pid} ***** SERVER FINISH *****");

error_log("${pid} RANGE : " . $_GET['range']);

switch (true) {
  case !isset($_SERVER['HTTP_AUTHORIZATION'], $_GET['range']):
  case !file_exists('/tmp/ml/AUTHORIZATION'):
  case $_SERVER['HTTP_AUTHORIZATION'] != file_get_contents('/tmp/ml/AUTHORIZATION'):
    header('Content-Type: text/plain');
    echo 'DUMMY';
    exit();
}

$range = $_GET['range'];

header('Content-Type: text/plain');
echo 'DUMMY';

header('Content-Type: text/html');

echo file_get_contents('/tmp/ml/' . $range);
?>
