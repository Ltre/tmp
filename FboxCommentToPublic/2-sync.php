<?php
while(1) {
    $r = file_get_contents('http://zx.mbox.duowan.com/?r=cron/syncPublicComment');
    print_r($r);
    sleep(10);
}
