<?php

$connection_info = parse_url(getenv('DATABASE_URL'));
$pdo = new PDO(
  "pgsql:host=${connection_info['host']};dbname=" . substr($connection_info['path'], 1),
  $connection_info['user'],
  $connection_info['pass']);

$sql = <<< __HEREDOC__
SELECT file, file_name, file_hash
  FROM t_file_yui_compressor
__HEREDOC__;

$statement = $pdo->prepare($sql);

$statement->execute();

$result = $statement->fetch();

error_log($result['file_name']);
error_log($result['file_hash']);
error_log($result['file']);
error_log(pg_unescape_bytea($result['file']));
file_put_contents('/tmp/test.txt', pg_unescape_bytea($result['file']));

$pdo = null;

exit();

?>
