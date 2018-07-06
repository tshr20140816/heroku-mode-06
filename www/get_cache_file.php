<?php
$pid = getmypid();

error_log("${pid} " . $_SERVER['PHP_AUTH_USER']);
error_log("${pid} " . $_SERVER['PHP_AUTH_PW']);

header('Content-Type: text/plain');
echo 'DUMMY';
?>
