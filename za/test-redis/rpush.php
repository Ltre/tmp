<?php
// php -c "D:\InstalledApp\wamp\bin\apache\apache2.4.18\bin\php.ini" rpush.php

$key = 'testbatchop';
$r = new Redis;
$r->connect('183.61.6.99', '6390');
//$r->connect('172.16.12.144', '6379');
$i = 0;
/* while (true) {
    $r->rPush($key, $i);
    echo "$i,";
    $i ++;
} */
for ($i = 0; $i < 10; $i++) {
    $r->rPush($key, $i);
}
print_r($r->lrange($key, 0, -1));