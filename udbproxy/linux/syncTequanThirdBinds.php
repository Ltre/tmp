<?php

$f1 = date('YmdHis');
@mkdir($f1, 0777, true);
$limit = 100;//在配置不高的机器上，分片和延迟最好都设置大点，否则CPU和IO忙不过来，很快卡死
$uSleep = 500;
$stopTime = time() + 86400;//最长执行一天
for ($i = 0; $i <= 1000 - $limit; $i += $limit) {
    $f2 = "{$f1}/{$i}";
    $code = '<?php
        $url = "http://udbproxy.duowan.com/?r=tequan/syncBinding&tableStartNum='.$i.'&taskId='.microtime(1).rand(0, 999999).'";
        while (1) {
            $log = file_get_contents($url);
            file_put_contents("log", $log, FILE_APPEND);
            echo $log;
            usleep('.$uSleep.');
            if (time() >= '.$stopTime.') break;
        }
    ';
    @mkdir($f2, 0777, true);
    $phpFile = 'fuck.php';
    $f3 = "{$f2}/{$phpFile}";
    $f4 = "{$f2}/fuck.sh";
    file_put_contents($f3, $code);
    file_put_contents($f4, "cd {$f2}; php {$phpFile}");
    echo("nohup ./{$f4} >{$f2}/nohup.out &")."\n";
}
