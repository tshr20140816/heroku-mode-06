<?php

$connection_info = parse_url(getenv('DATABASE_URL'));
$pdo = new PDO(
  "pgsql:host=${connection_info['host']};dbname=" . substr($connection_info['path'], 1),
  $connection_info['user'],
  $connection_info['pass']);

$sql = <<< __HEREDOC__
SELECT *
  FROM t_file_yui_compressor
__HEREDOC__;
$rc = $pdo->query($sql);

if ($rc === FALSE) {
  $rc = 0;
}

$pdo = null;

return $rc;
?>
