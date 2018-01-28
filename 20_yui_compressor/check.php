<?php

echo 'START';
echo $argv[1];
echo $argv[2];

$connection_info = parse_url(getenv('DATABASE_URL'));
$pdo = new PDO(
  "pgsql:host=${connection_info['host']};dbname=" . substr($connection_info['path'], 1),
  $connection_info['user'],
  $connection_info['pass']);

$sql = <<< __HEREDOC__
SELECT COUNT('X') cnt
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

echo $result['cnt'];

$pdo = null;

echo 'FINISH';

exit($result['cnt']);

?>
