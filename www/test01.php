<?php

$url = 'http://shop.rcc.jp/store/?kind=goods';
$encoding = 'shift_jis';
$global_pattern = '/<tr valign="top">(.+?)<\/table>/s';
$item_pattern = '/<td><a href="(.+?)"><img src=".+?".+?<h3>(.+?)<\/h3>/s';

$html = mb_convert_encoding(file_get_contents($url), 'UTF-8', $encoding);

//error_log($html);

$rc = preg_match($global_pattern, $html, $matches1);

//error_log($rc);
//error_log($matches1[1]);

$rc = preg_match_all($item_pattern, $matches1[1], $matches2, PREG_SET_ORDER);

error_log($rc);
error_log($matches2[1][1]);
error_log($matches2[1][2]);
error_log($matches2[2][1]);
error_log($matches2[2][2]);

/*
feed_title
feed_link

item_title
item_link
item_content
*/

?>
