<?php
// php -c "D:\InstalledApp\wamp\bin\apache\apache2.4.18\bin\php.ini" clear.php

$key = 'testbatchop';
$r = new Redis;
$r->connect('183.61.6.99', '6390');
//$r->connect('172.16.12.144', '6379');
$r->delete($key);