<?php

$url = 'http://udbproxy.duowan.com/?r=tequan/syncBinding';
while (1) {
    $log = file_get_contents($url);
    file_put_contents('log', $log, FILE_APPEND);
    echo $log;
    usleep(500);
}
