<?php

error_log('***** TEST MESSAGE START *****');

$fp = fopen("php://stdin","r");
$buf = "";
if ($fp > 0) {
  while(!feof($fp)) $buf .= fread($fp,4092);
  fclose($fp);
}

if (strpos($buf, 'Content-Type: text/html;') !== false)
{
  $arr_buf = preg_split('/^\r\n/m', $buf, 2);
  $header = $arr_buf[0];
  $body = $arr_buf[1];
  
  $header = preg_replace('/^X-Request.+\n/m', '', $header);
  $header = str_replace('Content-Length:', 'X-Content-Length:', $header);
  
  $body = str_replace('<TITLE>', '<HTML><HEAD><META HTTP-EQUIV="REFRESH" CONTENT="600"><TITLE>', $body);
  $body = str_replace('</TITLE>', '</TITLE></HEAD>', $body);

  $body = str_replace('http://' . $_SERVER['SERVER_NAME'] . ':80/-/builtin/icons/ysato/', '/icons/', $body);

  $body = preg_replace('/<FORM ACTION="..\/-search" METHOD=GET>.+?<\/FORM>/s', '', $body);
  
  $body = preg_replace('/<!-- generated by DeleGate\/x.x.x -->.+/s', '</BODY></HTML>', $body);
  
  $body = str_replace('<A HREF="../"><IMG BORDER=0 ALIGN=MIDDLE ALT="upper" SRC="/icons/up.gif"></A>', '', $body);
  
  $body = preg_replace('/^ *\r\n/m', '', $body);
  $body = preg_replace('/^  /m', ' ', $body);
  $body = preg_replace('/^ +</m', '<', $body);
  
  //$body = preg_replace('/^<small>.*?[Top.+?Up.+?A>$/m', '', $body);
  
  error_log($header);
  error_log("\r\n");
  error_log($body);
  
  $body = gzencode($body);
  
  $buf = $header;
  $buf .= "Content-Encoding: gzip\r\n";
  $buf .= "Content-Length: " . strlen($body) . "\r\n";
  $buf .= "\r\n";
  $buf .= $body;
}

echo $buf;

error_log('***** TEST MESSAGE FINISH *****');
?>
