<?php

// $argv[1] : file name (full path)
// $argv[2] : file hash

/*
foreach ($argv as $arg) {

}
*/

$connection_info = parse_url(getenv('DATABASE_URL'));
$pdo = new PDO(
  "pgsql:host=${connection_info['host']};dbname=" . substr($connection_info['path'], 1),
  $connection_info['user'],
  $connection_info['pass']);

$sql = <<< __HEREDOC__
SELECT file_data
  FROM t_file_yui_compressor
 WHERE file_name = :b_file_name
   AND file_hash = :b_file_hash
__HEREDOC__;

$statement = $pdo->prepare($sql);

$statement->execute(
  [':b_file_name' => pathinfo($argv[1], PATHINFO_BASENAME),
   //':b_file_hash' => $argv[2],
   ':b_file_hash' => file_get_contents($argv[1] . '.org')
  ]);

$result = $statement->fetch();

if ($result === FALSE) {
  $rc = 1;
} else {
  $rc = 0;
  //error_log($result['file_data']);
  file_put_contents($argv[1], $result['file_data']);
}

$pdo = null;

//error_log(hash('sha512', file_get_contents($argv[1] . '.org')));

exit($rc);

?>
