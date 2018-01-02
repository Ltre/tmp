<?php

$f1 = date('YmdHis');
@mkdir($f1, 0777, true);
$limit = 300;//在自用window机器上，分片和延迟最好都设置大点，否则CPU和IO忙不过来，很快卡死
$uSleep = 800;
for ($i = 0; $i <= 1000 - $limit; $i += $limit) {
    $code = '<?php
        $url = "http://udbproxy.duowan.com/?r=tequan/syncBinding&tableStartNum='.$i.'&taskId='.microtime(1).rand(0, 999999).'";
        while (1) {
            $log = file_get_contents($url);
            file_put_contents("log", $log, FILE_APPEND);
            echo $log;
            usleep('.$uSleep.');
        }
    ';
    $f2 = "{$f1}/{$i}";
    @mkdir($f2, 0777, true);
    $phpFile = 'fuck.php';
    $f3 = "{$f2}/{$phpFile}";
    $f4 = "{$f2}/fuck.bat";
    file_put_contents($f3, $code);
    file_put_contents($f4, "start \"UdbProxy Task: {$f2}\" php {$phpFile}");
    exec("start /D \"{$f1}\\{$i}\" fuck.bat");
}
