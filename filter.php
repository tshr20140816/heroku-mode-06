<?php
$pid = getmypid();

$uri = $_SERVER['REQUEST_URI'];

error_log("${pid} ***** FILTER MESSAGE START ***** ${uri}");

error_log("${pid} ***** SERVER START ***** ${uri}");
error_log(print_r($_SERVER, true));
error_log("${pid} ***** SERVER FINISH ***** ${uri}");

error_log("${pid} HTTP_USER_AGENT: " . $_SERVER['HTTP_USER_AGENT']);
error_log("${pid} HTTP_X_ACCESS_KEY: " . $_SERVER['HTTP_X_ACCESS_KEY']);
error_log("${pid} HTTP_X_HOST_NAME: " . $_SERVER['HTTP_X_HOST_NAME']);
error_log("${pid} HTTP_X_URL_DELEGATE_CACHE: " . $_SERVER['HTTP_X_URL_DELEGATE_CACHE']);
error_log("${pid} HTTP_AUTHORIZATION: " . $_SERVER['HTTP_AUTHORIZATION']);

// IE Edge 不可
if (preg_match('/(Trident|Edge)/', $_SERVER['HTTP_USER_AGENT']) || getenv('X_ACCESS_KEY') != $_SERVER['HTTP_X_ACCESS_KEY']) {
  error_log("${pid} #*#*#*#*# IE or Edge or X-Access-Key Unmatch #*#*#*#*#");
  header('HTTP', true, 403);

  $message =
    'D 403 ' .
    $_SERVER['SERVER_NAME'] . ' ' .
    $_SERVER['HTTP_X_FORWARDED_FOR'] . ' ' .
    $_SERVER['REMOTE_USER'] . ' ' .
    $_SERVER['REQUEST_METHOD'] . ' ' .
    $uri . ' ' .
    $_SERVER['HTTP_USER_AGENT'];

  loggly_log($message);

  error_log("${pid} ${res}");

  error_log("${pid} ***** FILTER MESSAGE FINISH ***** ${uri}");
  return;
}

error_log("${pid} ***** STDIN START ***** ${uri}");
$buf = file_get_contents('php://stdin');
error_log("${pid} ***** STDIN FINISH ***** ${uri}");

$tmp = explode('/', $uri);

$range = null;
$range_last_number = 0;
$range_start_number = 0;
if (strpos(end($tmp), '-') !== FALSE) {
  $range = end($tmp);
  $tmp = explode('-', $range);
  $range_start_number = $tmp[0];
  $range_last_number = end($tmp);
}
error_log("${pid} RANGE LAST NUMBER : ${range_last_number}");

$last_mail_number = 0;
if (preg_match('/.+A\sHREF="(\d+)?".+?"latest"/s', $buf, $m)) {
  $last_mail_number = $m[1];
}
error_log("${pid} LAST MAIL NUMBER : ${last_mail_number}");

if ($last_mail_number < $range_last_number) {
  $range_last_number = 0;
}

$arr_buf = preg_split('/^\r\n/m', $buf, 2);
$header = $arr_buf[0];
$body = $arr_buf[1];

// 余計なヘッダ削除
$header = preg_replace('/^X-Request.+\n/m', '', $header);
$header = preg_replace('/^ETag.+\n/m', '', $header);
$header = preg_replace('/^Expires.+\n/m', '', $header);
// 偽装
$header = preg_replace('/^Server: DeleGate.+$/m', 'Server: Apache', $header);
$header = preg_replace('/^DeleGate.+\n/m', '', $header);

if (strpos($header, 'Content-Type: text/html') !== false) {
  // イメージファイルでは残したいけどここでは不要
  $header = preg_replace('/^Last-Modified.+\n/m', '', $header);

  // 元サイズ
  $header = str_replace('Content-Length:', 'X-Content-Length:', $header);

  // 最新メールのレンジの場合のみ自動更新追加
  if ($range !== null && $range_last_number == 0) {
    $body = str_replace('<TITLE>', '<HTML><HEAD><META HTTP-EQUIV="REFRESH" CONTENT="600"><BASE HREF="https://'
                        . $_SERVER['HTTP_X_FORWARDED_HOST'] . '/ml/"><TITLE>R ', $body);  
  } else {  
    $body = str_replace('<TITLE>', '<HTML><HEAD><TITLE>', $body);
  }
  
  $replace_text = <<< __HEREDOC__
</TITLE>
<STYLE TYPE='text/css'>
a { text-decoration: none; font-weight: 500; }
</STYLE></HEAD>
__HEREDOC__;
  //$body = str_replace('</TITLE>', '</TITLE></HEAD>', $body);
  $body = str_replace('</TITLE>', $replace_text, $body);

  // アイコンはフロント側から取得
  $body = str_replace('http://' . $_SERVER['SERVER_NAME'] . ':80/-/builtin/icons/ysato/', '/icons/', $body);

  $body = preg_replace('/<FORM ACTION="..\/-search" METHOD=GET>.+?<\/FORM>/s', '', $body);

  if ($range !== null && $range_last_number == 0) {
    $last_mail_number = $last_mail_number - $last_mail_number % 10;
    $body = preg_replace('/<!-- generated by DeleGate\/x.x.x -->.+/s', '<A HREF="' . ($range_start_number - 100) . '-'
                         . $range_start_number . '" TARGET="_blank">PRE100</A><BR><A HREF="' . $last_mail_number . '-'
                         . ($last_mail_number + 50) . '">NEW50</A></BODY></HTML>', $body);
  } else {
    $body = preg_replace('/<!-- generated by DeleGate\/x.x.x -->.+/s', '</BODY></HTML>', $body);
  }

  $body = str_replace('<A HREF="../"><IMG BORDER=0 ALIGN=MIDDLE ALT="upper" SRC="/icons/up.gif"></A>', '', $body);

  $body = preg_replace('/<FONT .+?>.+?<\/FONT>/s', '', $body, 1);

  $body = preg_replace('/<small>.+?<\/small>/s', '', $body, 3);

  $body = preg_replace('/<(s|\/s)mall>/s', '', $body);
  $body = str_replace('</DT>', '</DT><BR><BR></B>', $body);

  $body = str_replace('<FORM ACTION="" METHOD=GET>', '', $body);
  $body = str_replace('</FORM>', '', $body);

  // 空白削除
  $body = preg_replace('/^ *\r\n/m', '', $body);
  $body = preg_replace('/^  /m', ' ', $body);
  $body = preg_replace('/^ +</m', '<', $body);

  $body = str_replace("</TD>\r\n", "</TD>", $body);
  $body = str_replace("</TR>\r\n", "</TR>", $body);
  $body = str_replace("<TD>\r\n", "<TD>", $body);
  $body = str_replace("<TR>\r\n", "<TR>", $body);
  $body = str_replace("<CODE>.</CODE>\r\n", '', $body);
  $body = str_replace("<DD>\r\n", "<DD>", $body);

  error_log("${pid} " . $uri);
  error_log($header);
  if (getenv('DELEGATE_LOG_LEVEL') != 'simple') {
    error_log("\r\n");
    error_log($body);
  }

  // 圧縮
  $buf = $header;
  $body_non_compress = $body;
  $body = gzencode($body, 9);

  $buf .= "Content-Encoding: gzip\r\n";
  $buf .= "Content-Length: " . strlen($body) . "\r\n";
  $buf .= "\r\n";
  $buf .= $body;
} else {
  error_log("${pid} " . $uri);
  error_log($header);
  $buf = $header;
  //$buf .= "Cache-Control: max-age=86400\r\n";
  $buf .= "\r\n";
  $buf .= $body;
}

echo $buf;

if ($range !== null && $range_last_number != 0) {
  // キャッシュ用データ送信
  for_cache_request($range, $body_non_compress);
}

$message =
  'D ' .
  explode(' ', $header, 3)[1] . ' ' .
  $_SERVER['SERVER_NAME'] . ' ' .
  $_SERVER['HTTP_X_FORWARDED_FOR'] . ' ' .
  $_SERVER['REMOTE_USER'] . ' ' .
  $_SERVER['REQUEST_METHOD'] . ' ' .
  $uri . ' ' .
  $_SERVER['HTTP_USER_AGENT'];

loggly_log($message);

error_log("${pid} ${message}");

error_log("${pid} ***** FILTER MESSAGE FINISH ***** ${uri}");

function loggly_log($message_) {
  $url_loggly = 'https://logs-01.loggly.com/inputs/' . getenv('LOGGLY_TOKEN') . '/tag/' . $_SERVER['SERVER_NAME'] . ',filter.php/';

  $ch = curl_init();
  curl_setopt_array($ch,
                    [CURLOPT_URL => $url_loggly,
                     CURLOPT_RETURNTRANSFER => TRUE,
                     CURLOPT_ENCODING => '',
                     CURLOPT_CONNECTTIMEOUT => 20,
                     CURLOPT_FOLLOWLOCATION => TRUE,
                     CURLOPT_MAXREDIRS => 3,
                     CURLOPT_POST => TRUE,
                     CURLOPT_HTTPHEADER => ['Content-Type: text/plain'],
                     CURLOPT_PATH_AS_IS => TRUE,
                     CURLOPT_POSTFIELDS => $message_,
                    ]);
  @curl_setopt($ch, CURLOPT_TCP_FASTOPEN, TRUE);
  curl_exec($ch);
  curl_close($ch);
}

function for_cache_request($name_, $data_) {
  $ch = curl_init();
  curl_setopt_array($ch,
                    [CURLOPT_URL => $_SERVER['HTTP_X_URL_DELEGATE_CACHE'],
                     CURLOPT_RETURNTRANSFER => TRUE,
                     CURLOPT_ENCODING => '',
                     CURLOPT_CONNECTTIMEOUT => 20,
                     CURLOPT_FOLLOWLOCATION => TRUE,
                     CURLOPT_MAXREDIRS => 3,
                     CURLOPT_POST => TRUE,
                     CURLOPT_HTTPHEADER => ['X-Access-Key: ' . $_SERVER['HTTP_X_ACCESS_KEY'],
                                            'X-Host-Name: ' . $_SERVER['HTTP_X_HOST_NAME'],
                                            'X-Authorization: ' . $_SERVER['HTTP_AUTHORIZATION'],
                                            'X-File-Name: ' . $name_,
                                           ],
                     CURLOPT_PATH_AS_IS => TRUE,
                     CURLOPT_POSTFIELDS => http_build_query(['data' => base64_encode($data_)]),
                    ]);
  @curl_setopt($ch, CURLOPT_TCP_FASTOPEN, TRUE);
  curl_exec($ch);
  curl_close($ch);
}
?>
