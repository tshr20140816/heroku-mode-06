<?php

$connection_info = parse_url(getenv('DATABASE_URL'));
$pdo = new PDO(
  "pgsql:host=${connection_info['host']};dbname=" . substr($connection_info['path'], 1),
  $connection_info['user'],
  $connection_info['pass']);

$sql = <<< __HEREDOC__
CREATE TABLE t_file_yui_compressor (
    file_name character varying(255) NOT NULL,
    file_hash character varying(255) NOT NULL,
    file_data text NOT NULL,
    change_time timestamp DEFAULT localtimestamp NOT NULL
);
__HEREDOC__;
$pdo->query($sql);

$sql = <<< __HEREDOC__
ALTER TABLE t_file_yui_compressor ADD CONSTRAINT table_key PRIMARY KEY(file_name, file_hash);
__HEREDOC__;
$pdo->query($sql);

$sql = <<< __HEREDOC__
TRUNCATE TABLE t_file_yui_compressor;
__HEREDOC__;
$pdo->query($sql);

$pdo = null;

?>
