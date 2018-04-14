#!/usr/local/php/bin/php
<?php

include 'config.php';
include 'Model.php';

$vids = file('vids.txt');//一行一个vid
@unlink('delete.sql');
$m = new Model('upload_list', 'mysql');
foreach ($vids as $vid) {
    $vid = trim($vid);
    var_dump($m->update(['vid' => $vid], ['status' => -9]));
    //$sql = "update upload_list set status = -9 where vid = {$vid} limit 1;\r\n";
    //echo $sql;
    //file_put_contents('delete.sql', $sql, FILE_APPEND);
}
