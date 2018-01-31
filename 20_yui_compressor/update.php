<?php

$connection_info = parse_url(getenv('DATABASE_URL'));
$pdo = new PDO(
  "pgsql:host=${connection_info['host']};dbname=" . substr($connection_info['path'], 1),
  $connection_info['user'],
  $connection_info['pass']);

$file_data = file_get_contents($argv[1]);

//error_log(pg_escape_bytea($file_data));

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

$statement = $pdo->prepare($sql);
$statement->execute(
  [':b_file_name' => pathinfo($argv[1], PATHINFO_BASENAME),
   ':b_file_hash' => $argv[2],
   ':b_file' => $file_data,
  ]);

$pdo = null;

?>
