#!/usr/local/php/bin/php
<?php

include 'config.php';
include 'Model.php';

$vids = file('vids.txt');//一行一个vid
$m = new Model('upload_list', 'mysql');
foreach ($vids as $vid) {
    $vid = trim($vid);
    $m->update(['vid' => $vid], ['letv_vu' => 1]);
    echo "vid: {$vid}, result: ";
    var_dump($m->update(['vid' => $vid], ['letv_vu' => '']));
    echo "\n";
    sleep(6);
}
