<?php
while(1) {
    $r = file_get_contents('http://comment3.duowan.com/?r=import/upFboxNum');
    print_r($r);
    sleep(10);
}
