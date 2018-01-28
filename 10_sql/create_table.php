<?php

$connection_info = parse_url(getenv('DATABASE_URL'));
$pdo = new PDO(
  "pgsql:host=${connection_info['host']};dbname=" . substr($connection_info['path'], 1),
  $connection_info['user'],
  $connection_info['pass']);

$sql = <<< __HEREDOC__
CREATE TABLE t_file_yui_compressor (
    file_name character varying(255) PRIMARY KEY,
    file_hash character varying(255) NOT NULL,
    file bytea NOT NULL,
    change_time timestamp DEFAULT localtimestamp NOT NULL
);
__HEREDOC__;
$pdo->query($sql) or die(print_r($db->errorInfo(), true));

$pdo = null;

?>
