<?php

$url = 'http://shop.rcc.jp/store/?kind=goods';
$encoding = 'shift_jis';
$global_pattern = '/<tr valign="top">(.+?)<\/table>/s';
$item_pattern = '/<td><a href="(.+?)"><img src="(.+?)".+?<h3>(.+?)<\/h3>/s';

$feed_title = 'RCCショップ';
$feed_link = 'http://shop.rcc.jp/store/';

$item_title = '__3__';
$item_link = 'http://shop.rcc.jp__1__';
$item_content = '<img src="http://shop.rcc.jp__2__"/>__3__';

$html = mb_convert_encoding(file_get_contents($url), 'UTF-8', $encoding);

//error_log($html);

$rc = preg_match($global_pattern, $html, $matches1);

//error_log($rc);
//error_log($matches1[1]);

$rc = preg_match_all($item_pattern, $matches1[1], $matches2, PREG_SET_ORDER);

$items_template = "<item><title>__TITLE__</title><link>__LINK__</link><description>__DESCRIPTION__</description><pubDate/></item>";

$items = array();
error_log($rc);
for($i = 0; $i < $rc; $i++) {
  error_log($matches2[$i][1]);
  error_log($matches2[$i][2]);
  error_log($matches2[$i][3]);
  $title = $item_title;
  $link = $item_link;
  $content = $item_content;
  for($j = 1; $j < count(($matches2[$i]); $j++) {
    $title = str_replace('__' . $j . '__', $matches2[$i][$j], $title);
    $link = str_replace('__' . $j . '__', $matches2[$i][$j], $link);
    $content = str_replace('__' . $j . '__', $matches2[$i][$j], $content);
  }
  $tmp = str_replace('__TITLE__', $title, $items_template);
  $tmp = str_replace('__LINK__', $link, $tmp);
  $tmp = str_replace('__DESCRIPTION__', $content, $tmp);
  $items[] = $tmp;
}

$xml_root_text = <<< '__HEREDOC__'
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
  <channel>
    <title>${feed_title}</title>
    <link>${$feed_link}</link>
    <description/>
    <language>ja</language>
    __ITEMS__
  </channel>
</rss>
'__HEREDOC__';

header('Content-Type: application/xml; charset=UTF-8');
echo str_replace('__ITEMS__', implode("\r\n", $items), $xml_root_text);

?>
