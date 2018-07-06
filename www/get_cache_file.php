<?php
$pid = getmypid();

error_log("${pid} PHP_AUTH_USER : " . $_SERVER['PHP_AUTH_USER']);
error_log("${pid} PHP_AUTH_PW : " . $_SERVER['PHP_AUTH_PW']);
error_log("${pid} RANGE : " . $_GET['range']);

switch (true) {
    case !isset($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'], $_GET['range']):
    case $_SERVER['PHP_AUTH_USER'] !== getenv('BASIC_USER'):
    case $_SERVER['PHP_AUTH_PW'] !== getenv('BASIC_PASSWORD'):
        header('WWW-Authenticate: Basic realm="u"');
        header('Content-Type: text/plain');
        die('NO LOGIN');
}

$range = $_GET['range'];

header('Content-Type: text/html');

echo file_get_contents('/tmp/ml/' . $range);
?>
