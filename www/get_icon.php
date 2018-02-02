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

header('Content-Type: image/vnd.microsoft.icon');
if ($result === FALSE) {
  error_log('File Not Found');
  error_log(getenv('REMOTE_PATH_2'));
  error_log(getenv('REMOTE_PATH_2') . 'feed-icons/' . $icon_file_name);
  $result = file_get_contents(getenv('REMOTE_PATH_2') . 'feed-icons/' . $icon_file_name);
  echo $result;
  $statement = $pdo->prepare('INSERT INTO t_icon_file (file_name, file_data) VALUES (:b_file_name, :b_file_data)');
  $statement->execute(
    [':b_file_name' => $icon_file_name,
     ':b_file_data' => base64_encode($result),
    ]);
} else {
  error_log('File Found');
  echo base64_decode($result['file_data']);
}

$pdo = null;

?>
