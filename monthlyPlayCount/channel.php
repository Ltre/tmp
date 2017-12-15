<?php

require_once 'config.php';
require_once 'Model.php';

$channelList = [
    'lol' => '英雄联盟',
    'df' => '地下城与勇士',
    'kan' => '娱乐',
    'wot' => '坦克世界',
    'ls' => '炉石传说',
    'coc' => '部落冲突',
    'www' => '首页',
    'cr' => '皇室战争',
    'wows' => '战舰世界',
    'wuxia' => '天涯明月刀',
    'wow' => '魔兽世界',
    '5253wzry' => '王者荣耀',
    'tv' => '电视游戏',
    'mil' => '军事',
    'pb' => '绝地求生',
    'pc' => '单机游戏',
    'ow' => '守望先锋',
    '5253video' => '手机游戏',
];

$monthList = [
    '201612',
    '201701',
    '201702',
    '201703',
    '201704',
    '201705',
    '201706',
    '201707',
    '201708',
    '201709',
    '201710',
    '201711',
];

$file = 'channel.csv';
@unlink($file);
$fp = fopen($file, 'w+');
$titles = array(
    'THIS_IS_CHANNEL_NAME' => '专区',
    'THIS_IS_CHANNEL' => '专区ID',
);
foreach ($monthList as $month) $titles[$month] = date('Y', strtotime($month.'01')).'年'.date('m', strtotime($month.'01')).'月';

//fwrite($fp, iconv('UTF-8', 'GB2312', join(',', $titles)."\r\n"));
fputcsv($fp, array_values($titles));

foreach ($channelList as $channel => $channelName) {
    $dataLine = [];
    foreach ($titles as $k => $v) {
        if ($k == 'THIS_IS_CHANNEL_NAME') {
            $dataLine[] = $channelName;
        } elseif ($k == 'THIS_IS_CHANNEL') {
            $dataLine[] = $channel;
        } else {//其它都是月份
            $table = "stats_video_play_day_{$k}";
            $m = new Model($table, 'mysql');
            $sql = "select sum(load_num) s from {$table} where channel = '{$channel}'";
            $rs = $m->query($sql);
            $dataLine[] = $sum = (int) @$rs[0]['s'];
            echo "writing SUM = {$sum} where channel = {$channel} and month = {$k} ... \n";
        }
    }
    fputcsv($fp, $dataLine);
}

fclose($fp);
