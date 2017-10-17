<?php
// php -c "D:\InstalledApp\wamp\bin\apache\apache2.4.18\bin\php.ini" ltrim.php
$key = 'testbatchop';
$r = new Redis;
$r->connect('183.61.6.99', '6390');
//$r->connect('172.16.12.144', '6379');

$len = 5;

/* $r->ltrim($key, $len, -1);
print_r($r->lrange($key, 0, -1));
die; */

while (true) {
    $records = $r->lrange($key, 0, $len - 1);
    $r->ltrim($key, $len, -1);
    echo "-------------------------------\r\n";
    echo "read records length: ".count($records).", remain queue length: ".$r->llen($key)."\r\n";
    echo "read start is: ".$records[0]."\r\n";
    echo "read end is: ".end($records)."\r\n";
    sleep(1);
}
