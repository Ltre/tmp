<?php

include 'hehe/config.php';
include 'hehe/lib.php';
include 'hehe/dwHttp.php';
include 'hehe/Model.php';

$source = 'bbspost';
$batch = '20180528';
$p = 1;
$limit = 100;
$sqlite = new Model("collect/{$source}-{$batch}.db", $source);
$list = $sqlite->select('', '', '', [$p, $limit, 10])
echo '<meta charset="utf-8">'
foreach ($list as $v) {
    echo "<div></div>";
    //addslashes($v)
}