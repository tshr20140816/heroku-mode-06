<?php

// file_name : /ttrss/feed-icons/nnn.ico

$icon_file_name = pathinfo($_GET['file_name'], PATHINFO_BASENAME);

error_log($icon_file_name);

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
  error_log('File Not Found');
  error_log(getenv('REMOTE_PATH_2'));
  error_log(getenv('REMOTE_PATH_2') . 'feed-icons/' . $icon_file_name);
  $http_response_header = null;
  $http_response_header[] = '';
  $tmp = explode(getenv('REMOTE_PATH_2'), ':');
  $request_server = $tmp[0];
  error_log($request_server);
  $context = [
    'http' => [
      'method' => 'GET',
      'header' => [
        'User-Agent: Love Love Show',
        "X-Request-Server: " . $request_server,
        ]],
    'ssl' => [
      'verify_peer' => false,
      'verify_peer_name' => false,
      ]];
  $result = file_get_contents('https://' . getenv('REMOTE_PATH_2') . 'feed-icons/' . $icon_file_name, false, stream_context_create($context));
  if (strpos($http_response_header[0], '200') !== FALSE) {
    header('Content-Type: image/vnd.microsoft.icon');
    echo $result;
    $statement = $pdo->prepare('INSERT INTO t_icon_file (file_name, file_data) VALUES (:b_file_name, :b_file_data)');
    $statement->execute(
      [':b_file_name' => $icon_file_name,
       ':b_file_data' => base64_encode($result),
      ]);
  } else {
    http_response_code(503);
  }
} else {
  error_log('File Found');
  header('Content-Type: image/vnd.microsoft.icon');
  echo base64_decode($result['file_data']);
}

$pdo = null;

?>
