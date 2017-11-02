<?php

$pid = getmypid();
$buf = file_get_contents('php://stdin');

file_get_contents(getenv('TEST_URL') . '?' . $pid);

?>
