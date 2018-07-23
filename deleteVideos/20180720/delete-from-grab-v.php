#!/usr/local/php/bin/php
<?php
//此脚本需要放到抓取的机器grab-v.duowan.com运行

include 'config.php';
include 'Model.php';

$vids = file('vids.txt');//一行一个vid
$objGrabVideo = new Model('grab_video', 'mysql_grab_v');
$objGrabUrlTask = new Model('grab_url_task', 'mysql_grab_v');
foreach ($vids as $vid) {
    $vid = trim($vid);
    $objGrabVideo->update(['vid' => $vid], ['is_del' => 1]);
    $objGrabUrlTask->update(['vid' => $vid], ['is_del' => 1]);
    usleep(50);
}
