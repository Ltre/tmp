#!/usr/local/php/bin/php
<?php

include 'config.php';
include 'Model.php';

$vids = file('vids.txt');//一行一个vid
$m = new Model('upload_list', 'mysql');
$collect = [];
foreach ($vids as $vid) {
    $vid = trim($vid);
    $up = $m->find(['vid' => $vid]);
    @$collect[$up['udb'].'_'.date('Ymd', $up['upload_start_time'])] ++;
}
print_r($collect);
