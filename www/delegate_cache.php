<?php

$pid = getmypid();

if (getenv('X_ACCESS_KEY') != $_SERVER['HTTP_X_ACCESS_KEY']) {
  exit();
}

$post_data = $_POST;

$name = $_SERVER['HTTP_X_FILE_NAME'];

file_put_contents('/tmp/cache_delegate/' . $name, $post_data);

?>
