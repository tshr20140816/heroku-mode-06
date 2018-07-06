<?php
$pid = getmypid();

error_log("${pid} PHP_AUTH_USER : " . $_SERVER['PHP_AUTH_USER']);
error_log("${pid} PHP_AUTH_PW : " . $_SERVER['PHP_AUTH_PW']);

switch (true) {
    case !isset($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']):
    case $_SERVER['PHP_AUTH_USER'] !== getenv('BASIC_USER'):
    case $_SERVER['PHP_AUTH_PW'] !== getenv('BASIC_PASSWORD'):
        header('WWW-Authenticate: Basic realm="dummy"');
        header('Content-Type: text/plain');
        die('NO LOGIN');
}

header('Content-Type: text/plain');
echo 'DUMMY';
?>
