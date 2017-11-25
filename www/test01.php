<?php

$url = 'http://shop.rcc.jp/store/?kind=goods';
$encoding = 'shift_jis';
$global_pattern = '/<tr valign="top">(.+?)</table>/s';
$item_pattern = '/<td><a href="(.+?)"><img src=".+?".+<h3>(.+?)<\/h3>/s';

$html = file_get_contents($url);

$rc = preg_match()

/*
feed_title
feed_link

item_title
item_link
item_content
*/

?>
