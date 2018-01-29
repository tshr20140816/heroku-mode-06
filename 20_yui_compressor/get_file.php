<?php

// $argv[1] : file name
// $argv[2] : file hash

$connection_info = parse_url(getenv('DATABASE_URL'));
$pdo = new PDO(
  "pgsql:host=${connection_info['host']};dbname=" . substr($connection_info['path'], 1),
  $connection_info['user'],
  $connection_info['pass']);

$sql = <<< __HEREDOC__
SELECT file
  FROM t_file_yui_compressor
 WHERE file_name = :b_file_name
   AND file_hash = :b_file_hash
__HEREDOC__;

$statement = $pdo->prepare($sql);

$statement->execute(
  [':b_file_name' => $argv[1],
   ':b_file_hash' => $argv[2],
  ]);

$result = $statement->fetch();

if ($result === FALSE) {
  $rc = 0;
} else {
  $rc = 1;
  file_put_contents($argv[1], pg_unescape_bytea($result['file']));
}

$pdo = null;

exit($rc);

?>
