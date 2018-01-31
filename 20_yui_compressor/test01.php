<?php

$connection_info = parse_url(getenv('DATABASE_URL'));
$pdo = new PDO(
  "pgsql:host=${connection_info['host']};dbname=" . substr($connection_info['path'], 1),
  $connection_info['user'],
  $connection_info['pass']);

$sql = <<< __HEREDOC__
SELECT file_name, file_hash, file_data
  FROM t_file_yui_compressor
__HEREDOC__;

$statement = $pdo->prepare($sql);

$statement->execute();

$result = $statement->fetch();

error_log($result['file_name']);
error_log($result['file_hash']);
error_log($result['file_data']);
file_put_contents('/tmp/test.txt', $result['file_data']);

$pdo = null;

exit();

?>
