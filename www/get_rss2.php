<?php

$url = 'https://raw.githubusercontent.com/tshr20140816/heroku-mode-03/master/70_etc/asin.txt?' . time();
$asins = explode("\n", file_get_contents($url));

$options = [
  'http' => [
    'method' => 'GET',
    'header' => 'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:56.0) Gecko/20100101 Firefox/59.0',
  ],
];
$context = stream_context_create($options);

foreach($asins as $asin) {
  if (strlen($asin) < 10) {
    continue;
  }
  error_log($asin);
  $url = 'https://www.amazon.co.jp/dp/' . $asin;
  $html = file_get_contents($url, false, $context);
  
  $rc = preg_match('/<title>(.+?)<\/title>/', $html, $matches);
  error_log($matches[1]);
  $title = $matches[1];

  $rc = preg_match('/data-asin-price="(.+?)"/', $html, $matches);
  error_log($matches[1]);
  $price = $matches[1];

  $items_template = '<item><title>__TITLE__</title><link>__LINK__</link><description>__DESCRIPTION__</description><pubDate/></item>';

  $description = $price . '&lt;br&gt;' . $title;
  $title = $title . ' ' . $price;
  $link = $url . '?dummy=' . $price;

  $tmp = str_replace('__TITLE__', $title, $items_template);
  $tmp = str_replace('__LINK__', $link, $tmp);
  $tmp = str_replace('__DESCRIPTION__', $description, $tmp);
  $items[] = $tmp;
}

$xml_root_text = <<< __HEREDOC__
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
  <channel>
    <title>Amazon Price</title>
    <link>https://www.amazon.co.jp/</link>
    <description/>
    <language>ja</language>
    __ITEMS__
  </channel>
</rss>
__HEREDOC__;
header('Content-Type: application/xml; charset=UTF-8');
echo str_replace('__ITEMS__', implode("\r\n", $items), $xml_root_text);

?>
