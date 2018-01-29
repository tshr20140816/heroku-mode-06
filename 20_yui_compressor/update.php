<?php

$connection_info = parse_url(getenv('DATABASE_URL'));
$pdo = new PDO(
  "pgsql:host=${connection_info['host']};dbname=" . substr($connection_info['path'], 1),
  $connection_info['user'],
  $connection_info['pass']);

$sql = <<< __HEREDOC__
INSERT INTO t_file_yui_compressor
( file_name
 ,file_hash
 ,file
) VALUES (
  :b_file_name
 ,:b_file_hash
 ,:b_file
)
__HEREDOC__;

$pdo = null;

?>
