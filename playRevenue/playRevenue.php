<?php
/**
 * 同步PlayRevenue
 * $start开始日期、$end结束日期、$step日期间隔
 */
$start = '20170101';
$end = date('Ymd');
$step = 5;
$i = 0;
$dir = 'phpcache';
@mkdir($dir, 0777);
$execFile = "exec.bat";
@unlink("{$dir}/{$execFile}");
for ($date = $start; $date <= $end; ) {
    if (0 == intval($i++ % $step)) {        
        $taskId = sha1(rand(0, 100000).'-'.$date);
        $c = "<?php
                while(1) {
                \$r = file_get_contents('http://huya.cms.duowan.com/cron/syncPlayRevenue?start={$date}&taskId={$taskId}');
                print_r(\$r);
            }";
        $currPhp = "revenuetmp{$date}.php";
        file_put_contents("{$dir}/{$currPhp}", $c);
        file_put_contents("{$dir}/{$execFile}", "start php {$currPhp}\r\n", FILE_APPEND);
    }
    $date = date('Ymd', strtotime($date.' +1 day'));
}
exec("start /D {$dir} {$execFile}");
//4216,5228,5356