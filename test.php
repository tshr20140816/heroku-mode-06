<?php

error_log('***** TEST MESSAGE START *****');

$fp = fopen("php://stdin","r");
$buf="";
if ($fp>0) {
    while(!feof($fp)) $buf .= fread($fp,4092);
    fclose($fp);
}

$buf = str_replace('Content-Length:', 'X-Content-Length:', $buf);

error_log($buf);

echo $buf;

error_log('***** TEST MESSAGE FINISH *****');
?>
