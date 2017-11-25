<?php

$url = 'http://shop.rcc.jp/store/?kind=goods';
$encoding = 'shift_jis';
$global_pattern = '/<tr valign="top">(.+?)<\/table>/s';
$item_pattern = '/<td><a href="(.+?)"><img src="(.+?)".+?<h3>(.+?)<\/h3>/s';

$feed_title = 'RCCショップ';
$feed_link = 'http://shop.rcc.jp/store/';

$item_title = '__3__';
$item_link = 'http://shop.rcc.jp__1__';
$item_description = '&lt;img src="http://shop.rcc.jp__2__"/&gt;__3__';

$items_template = "<item><title>__TITLE__</title><link>__LINK__</link><description>__DESCRIPTION__</description><pubDate/></item>";

$html = mb_convert_encoding(file_get_contents($url), 'UTF-8', $encoding);

$rc = preg_match($global_pattern, $html, $matches1);

$items = array();

$rc = preg_match_all($item_pattern, $matches1[1], $matches2, PREG_SET_ORDER);
for ($i = 0; $i < $rc; $i++) {
  $title = $item_title;
  $link = $item_link;
  $description = $item_description;
  for ($j = 1; $j < count($matches2[$i]); $j++) {
    $title = str_replace('__' . $j . '__', $matches2[$i][$j], $title);
    $link = str_replace('__' . $j . '__', $matches2[$i][$j], $link);
    $description = str_replace('__' . $j . '__', $matches2[$i][$j], $description);
  }
  $tmp = str_replace('__TITLE__', $title, $items_template);
  $tmp = str_replace('__LINK__', $link, $tmp);
  $tmp = str_replace('__DESCRIPTION__', $description, $tmp);
  $items[] = $tmp;
}

$xml_root_text = <<< __HEREDOC__
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
  <channel>
    <title>${feed_title}</title>
    <link>${feed_link}</link>
    <description/>
    <language>ja</language>
    __ITEMS__
  </channel>
</rss>
__HEREDOC__;

header('Content-Type: application/xml; charset=UTF-8');
echo str_replace('__ITEMS__', implode("\r\n", $items), $xml_root_text);
?>
