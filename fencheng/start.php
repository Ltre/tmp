<?php
mb_internal_encoding("GB2312");//本文件运行编码环境为简体中文

function rmdir_force($dir){
    if (! is_dir($dir)) return;
    $list = scandir($dir);
    foreach ($list as $v) {
        if (in_array($v, array('.', '..'))) continue;
        $curr = "{$dir}/{$v}";
        if ('dir' == filetype($curr)) rmdir_force($curr);
        else unlink($curr);
    }
    reset($list);
    @rmdir($dir);
}

//选择任务所用的php文件，通过如下命令传入参数：php start.php "fencheng-lite.php"
$phpFile = $_SERVER['argv'][1];
if (! in_array($phpFile, array('fencheng-lite.php', 'fencheng.php', 'fencheng-calcmore.php'))) die('argv[1] for $phpFile is error!');

$pieceLen = 20;//每个任务计算的UID个数
$uidsFile = 'uids.txt';
$uids = file($uidsFile);
$uidPieces = array_chunk($uids, $pieceLen);

//创建缓存目录，执行并行计算
$cacheDir = 'cache_fencheng_'.date('YmdHis');
$finishFile = 'finish.is';//用于上报任务完成时所写入的文件名
rmdir_force($cacheDir);
@mkdir($cacheDir, 0777, true);
foreach ($uidPieces as $k => $piece) {
    $currPath = "{$cacheDir}/{$k}";
    @mkdir($currPath, 0777, true);
    foreach ($piece as $line) file_put_contents("{$currPath}/{$uidsFile}", $line, FILE_APPEND);
    @copy($phpFile, "{$currPath}/{$phpFile}");
    file_put_contents("{$currPath}/{$phpFile}", "; file_put_contents('{$finishFile}', '1');", FILE_APPEND);//注入代码：以写文件方式上报是否执行完成
    file_put_contents("{$currPath}/{$phpFile}.bat", "start \"当前任务：{$cacheDir}\\{$k}\" php {$phpFile}");
    exec("start /D \"{$cacheDir}\\{$k}\" {$phpFile}.bat");
}

//等待所有任务完成后，合并执行结果（目前只针对upload_uid目录进行合并，其它暂没必要）
$waitDur = 0;
$sleepGap = 2;
while (1) {
    $wait = false;
    foreach ($uidPieces as $k => $piece) {
        if (! file_exists("{$cacheDir}/{$k}/{$finishFile}")) $wait = true;
    }
    if ($wait) {
        echo "waiting for the result of calculate, has lasted for {$waitDur} s ...\r\n";
        sleep($sleepGap);
    } else {
        @mkdir("{$cacheDir}/result", 0777, true);
        $collectTotalCsv = array();
        $collectAdTotalCsv = array();
        $varNameMap = array(
            'total.csv' => 'collectTotalCsv', 
            'ad-total.csv' => 'collectAdTotalCsv',
        );
        foreach ($uidPieces as $k => $piece) {
            $dataPath = "{$cacheDir}/{$k}/data/upload_uid";
            $list = scandir($dataPath);
            foreach ($list as $v) {
                if (in_array($v, array('.', '..'))) continue;
                if (in_array($v, array('total.csv', 'ad-total.csv'))) {
                    $tmpCollV = $varNameMap[$v];
                    foreach (file("{$dataPath}/{$v}") as $vv) {
                        if ('' == trim($vv)) continue;
                        if (false !== array_search($vv, $$tmpCollV)) continue;//去除重复的标题行
                        $$tmpCollV[] = $vv;//这里如果执行报错，就换成array_push($$tmpCollV, $vv)
                    }//分别收集齐ad-total.csv和total.csv的数据
                } else {
                    @copy("{$dataPath}/{$v}", "{$cacheDir}/result/{$v}");//复制每个uid对应的csv
                }
            }
        }
        foreach ($varNameMap as $kFile => $vVarName) {
            foreach ($$vVarName as $vLine) {
                file_put_contents("{$cacheDir}/result/{$kFile}", $vLine, FILE_APPEND);
            }//写入合并后的ad-total.csv和total.csv
        }
        echo 'calc finish!';
        break;
    }
    $waitDur += $sleepGap;
}