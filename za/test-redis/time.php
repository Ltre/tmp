<?php

$key = 'testbatchop';
$r = new Redis;
$r->connect('183.61.6.99', '6390');
//$r->connect('172.16.12.144', '6379');

$s = microtime(1);
echo "wait \r\n";
for ($i = 0; $i < 1000; ++ $i) {
    $r->rpush($key, $i);
}
echo "\r\n";
$e = microtime(1);

echo ($e - $s)."\r\n";
