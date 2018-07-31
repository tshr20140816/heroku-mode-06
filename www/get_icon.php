<?php

// file_name : /ttrss/feed-icons/nnn.ico

$icon_file_name = pathinfo($_GET['file_name'], PATHINFO_BASENAME);

$log_prefix = getmypid() . ' ' . $icon_file_name . ' ';

error_log($log_prefix);

if (strpos($_SERVER['HTTP_USER_AGENT'], '2-33-9-51') === FALSE) {
  header('Content-Type: image/vnd.microsoft.icon');
  echo file_get_contents('/app/www/black.ico');
  exit();
}

$connection_info = parse_url(getenv('DATABASE_URL'));
$pdo = new PDO(
  "pgsql:host=${connection_info['host']};dbname=" . substr($connection_info['path'], 1),
  $connection_info['user'],
  $connection_info['pass']);

$sql = <<< __HEREDOC__
SELECT file_data
  FROM t_icon_file
 WHERE file_name = :b_file_name
__HEREDOC__;

$statement = $pdo->prepare($sql);

$statement->execute(
  [':b_file_name' => $icon_file_name,
  ]);
$result = $statement->fetch();

if ($result === FALSE) {
  loggly_log($log_prefix . 'File Not Found');
  error_log($log_prefix . getenv('REMOTE_PATH_2'));
  error_log($log_prefix . getenv('REMOTE_PATH_2') . 'feed-icons/' . $icon_file_name);
  $http_response_header = null;
  $http_response_header[] = '';
  $tmp = explode(':', getenv('REMOTE_PATH_2'));
  $x_key = $tmp[0];
  error_log($log_prefix . $x_key);
  $url = 'https://' . getenv('REMOTE_PATH_2') . 'feed-icons/' . $icon_file_name';
  list($contents, $http_code) = get_contents($url);
  error_log($log_prefix . $http_code);
  
  if ($http_code === '200') {
    header('Content-Type: image/vnd.microsoft.icon');
    echo $contents;
    $statement = $pdo->prepare('INSERT INTO t_icon_file (file_name, file_data) VALUES (:b_file_name, :b_file_data)');
    $statement->execute(
      [':b_file_name' => $icon_file_name,
       ':b_file_data' => base64_encode($contents),
      ]);
  } else {
    header('Content-Type: image/vnd.microsoft.icon');
    echo file_get_contents('/app/www/black.ico');
  }
} else {
  error_log($log_prefix . 'File Found');
  header('Content-Type: image/vnd.microsoft.icon');
  echo base64_decode($result['file_data']);
}

$pdo = null;

exit();

function get_contents($url_) {
  $pid = getmypid();
  $ch = curl_init();
  
  $tmp = explode(':', getenv('REMOTE_PATH_2'));
  $x_key = $tmp[0];
  
  curl_setopt_array($ch,
                    [CURLOPT_URL => $url_,
                     CURLOPT_SSL_VERIFYPEER => FALSE,
                     CURLOPT_RETURNTRANSFER => TRUE,
                     CURLOPT_ENCODING => '',
                     CURLOPT_CONNECTTIMEOUT => 20,
                     CURLOPT_FOLLOWLOCATION => TRUE,
                     CURLOPT_MAXREDIRS => 3,
                     CURLOPT_FILETIME => TRUE,
                     CURLOPT_PATH_AS_IS => TRUE,
                     CURLOPT_USERAGENT => 'Love Love Show',
                     CURLOPT_HTTPHEADER => ['X-Key: ' . $x_key, 'X-Request-Server: ' . getenv('HEROKU_APP_NAME')],
                    ]);
  $contents = curl_exec($ch);
  $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  
  curl_close($ch);
  
  return [$contents, $http_code];
}

function loggly_log($message_) {
  error_log($message_);
  
  $url_loggly = 'https://logs-01.loggly.com/inputs/' . getenv('LOGGLY_TOKEN') . '/tag/relay_rss,' . getenv('HEROKU_APP_NAME') . '/';
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
                     // CURLOPT_SSL_FALSESTART => TRUE,
                     CURLOPT_PATH_AS_IS => TRUE,
                     CURLOPT_POSTFIELDS => $message_,
                    ]);
  curl_exec($ch);
  curl_close($ch);
}
?>
