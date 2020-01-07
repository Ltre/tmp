<?php

include 'config.php';
include 'Model.php';

$m1 = new Model('order_info', 'mysql');
$m2 = new Model('order_info', 'mysql_ali');
$list = $m1->query("select * from order_info where order_id > 230975");


file_put_contents('export2migrate.json', json_encode($list));//多玩机器无法连接阿里，故导出

die;
foreach ($list as $v) {
    //var_dump($v);
    var_dump($m2->insert($v));
    echo "\n";
}
