<?php
$pid = getmypid();

error_log("${pid} PHP_AUTH_USER : " . $_SERVER['PHP_AUTH_USER']);
error_log("${pid} PHP_AUTH_PW : " . $_SERVER['PHP_AUTH_PW']);

header('Content-Type: text/plain');
echo 'DUMMY';
?>
