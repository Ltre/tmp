#!/usr/local/php/bin/php
<?php

include 'config.php';
include 'Model.php';

$vids = file('vids.txt');//一行一个vid
$m = new Model('upload_list', 'mysql');
$collect = $m->query("SELECT vid FROM upload_list where udb = '2287177400yy' and upload_start_time >= ".strtotime('2018-07-02'));
print_r($collect);
