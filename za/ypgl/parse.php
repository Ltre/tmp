<?php

//view-source:http://172.16.12.111:65432/parse.php?url=http://s1.dwstatic.com/author-grab/20170802/11/4626122.jpeg


include "lib/phpQuery/phpQuery.php";
include "lib/dwHttp.php";

header("Content-type: text/html; charset=UTF-8");

$url = "http://tu.baidu.com/n/pc_search?" . http_build_query(array(
  'queryImageUrl' => $_GET['url'],
  'fm' => 'result_camera',
  'uptype' => 'paste',
  'drag' => 1,
));

$c = file_get_contents($url);

preg_match('/\'guessWord\'\: \'(.+)\'\.split\(\'\*\'\)\,/', $c, $matches);

echo $matches[1];

die;

echo $c;
die;

phpQuery::newDocumentHTML(file_get_contents($url));

$firstTxt = pq('#guessInfo > div.guess-info-text')->text();
var_dump($firstTxt);
die;
if (false !== strpos($firstTxt, '对该图片的最佳猜测')){
  echo pq('#guessInfo > div.guess-info-text > a')->text();
}
