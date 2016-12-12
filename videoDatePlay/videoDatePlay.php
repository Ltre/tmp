<?php
/**
 * 生成批量请求
 * $start开始日期、$end结束日期、$step日期间隔
 */
$step = 100;
$i = 0;
$start = '20151201';
$dir = 'phpcache';
@mkdir($dir, 0777);
$execFile = "exec.bat";
@unlink("{$dir}/{$execFile}");
for ($date = $start; $date <= date('Ymd'); ) {
    if (0 == intval($i++ % $step)) {        
        $taskId = sha1(rand(0, 100000).'-'.$date);
        $end = date('Ymd', strtotime($date." +".($step-1)."days"));
        if ($end >= date('Ymd')) $end = '';
        $c = "<?php
                while(1) {
                \$r = file_get_contents('http://huya.cms.duowan.com/cron/SyncVideoDatePlay?start={$date}&end={$end}&taskId={$taskId}');
                print_r(\$r);
            }";
        $currPhp = "videodateplay{$date}.php";
        file_put_contents("{$dir}/{$currPhp}", $c);
        file_put_contents("{$dir}/{$execFile}", "start php {$currPhp}\r\n", FILE_APPEND);
    }
    $date = date('Ymd', strtotime($date.' +1 day'));
}
exec("start /D {$dir} {$execFile}");
//4216,5228,5356