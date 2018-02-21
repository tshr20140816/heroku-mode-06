<?php

// $argv[1] : file name (full path)

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

foreach ($argv as $arg) {
  error_log($arg);
  $statement->execute(
    [':b_file_name' => pathinfo($arg, PATHINFO_BASENAME),
     ':b_file_hash' => hash('sha512', file_get_contents($arg . '.org'))
    ]);

  $result = $statement->fetch();

  if ($result === FALSE) {
    $rc = 1;
  } else {
    $rc = 0;
    file_put_contents($arg, $result['file_data']);
  }
}

$pdo = null;

exit($rc);

?>
