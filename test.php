<?php

error_log('***** TEST MESSAGE START *****');

$fp = fopen("php://stdin","r");
$buf="";
if ($fp>0) {
    while(!feof($fp)) $buf .= fread($fp,4092);
    fclose($fp);
}

$buf = str_replace('Content-Length:', 'X-Content-Length:', $buf);

$buf = str_replace('http://' . $_SERVER['SERVER_NAME'] . ':80/-/builtin/icons/ysato/', './icons/', $buf);

error_log($buf);

echo $buf;

error_log('***** TEST MESSAGE FINISH *****');
?>
