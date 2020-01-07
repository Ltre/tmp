<?php

include 'config.php';
include 'Model.php';

$m2 = new Model('order_info', 'mysql_ali');

$j = file_get_contents('export2migrate.json');//多玩机器无法连接阿里，故采用导入
$list = json_decode($j, true);
foreach ($list as $v) {
    var_dump($v);
    //var_dump($m2->insert($v));
    echo "\n";
}
