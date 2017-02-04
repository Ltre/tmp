<?php
/**
 * 生成批量请求
 * $start开始日期、$end结束日期、$step日期分段长度
 */
$processNum = 6;//控制总进程数(实际可能会多1或少1)
$yourStep = 0;//设置为0时，将采用根据start和end推算出的最佳间隔；设置大于0的整数时，$processNum将无效
$i = 0;
$start = '20170101';
$end = '20170131';//若不限制，则设置为空串
$gap = (strtotime($end ?: date('Ymd')) - strtotime($start ?: date('Ymd'))) / 86400 + 1;//日期总间隔
$step = intval($yourStep ?: floor($gap > $processNum ? $gap / $processNum : 1));
$dir = 'phpcache';
$uids = array(
    '9604747',
    '814817470',
    '1440170871',
    '1002099688',
    '1268010099',
    '1468594965',
    '813478263',
    '4011410',
    '1617689824',
);
@mkdir($dir, 0777);
$execFile = "exec.bat";
@unlink("{$dir}/{$execFile}");
for ($date = $start; $date <= ($end > date('Ymd') ? date('Ymd') : $end); ) {
    $dateTo = date('Ymd', strtotime($date." +".($step-1)." day"));
    if ($dateTo > $end) $dateTo = $end;
    $whileBody = '';
    foreach ($uids as $uid) {
        $taskId = sha1(rand(0, 100000).$date.$uid);
        $whileBody .= 
            "\$r = file_get_contents('http://huya.cms.duowan.com/cron/SyncVideoDatePlay?yyuid={$uid}&start={$date}&end={$dateTo}&taskId={$taskId}');
            print_r(\$r);\r\n";
    }
    $c = "<?php
            while(1) {
            {$whileBody}
        }";
    $currPhp = "process_{$date}.php";
    file_put_contents("{$dir}/{$currPhp}", $c);
    file_put_contents("{$dir}/{$execFile}", "start php {$currPhp}\r\n", FILE_APPEND);
    $date = date('Ymd', strtotime("{$date} +{$step} day"));
}
exec("start /D {$dir} {$execFile}");
//4216,5228,5356