<?php

$url = 'http://shop.rcc.jp/store/?kind=goods';
$encoding = 'shift_jis';
$global_pattern = '/<tr valign="top">(.+?)<\/table>/s';
$item_pattern = '/<td><a href="(.+?)"><img src=".+?".+<h3>(.+?)<\/h3>/s';

$html = mb_convert_encoding(file_get_contents($url), 'UTF-8', $encoding);

//error_log($html);

$rc = preg_match($global_pattern, $html, $matches);

error_log($rc);
error_log(var_dump($matches));
/*
feed_title
feed_link

item_title
item_link
item_content
*/

?>
