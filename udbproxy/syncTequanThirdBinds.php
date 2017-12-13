<?php

$f1 = date('YmdHis');
@mkdir($f1, 0777, true);

for ($i = 0; $i <= 1000 - 50; $i += 50) {
    $code = '<?php
        $url = "http://udbproxy.duowan.com/?r=tequan/syncBinding&tableStartNum='.$i.'&taskId='.microtime(1).rand(0, 999999).'";
        while (1) {
            $log = file_get_contents($url);
            file_put_contents("log", $log, FILE_APPEND);
            echo $log;
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
