#!/usr/local/php/bin/php
<?php

include 'config.php';
include 'Model.php';

//$vids = file('vids.txt');//一行一个vid
$m = new Model('upload_list', 'mysql');
$list = $m->query("SELECT vid FROM upload_list where udb = '2287177400yy' and upload_start_time >= ".strtotime('2018-07-02')) ?: [];
foreach ($list as $v) {
    $vid = trim($v['vid']);
    $m->update(['vid' => $vid], ['letv_vu' => 1]);
    echo "vid: {$vid}, result: ";
    var_dump($m->update(['vid' => $vid], ['letv_vu' => '']));
    echo "\n";
    sleep(1);
}
