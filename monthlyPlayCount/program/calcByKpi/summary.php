<?php

require_once 'config.php';

$tmpDir = @$_SERVER['argv'][1] ?: date('YmdHis');
$waitDur = 0;
$sleepGap = 2;
$monthList = $GLOBALS['monthList'];
$udbList = file('udblist.csv');
foreach ($udbList as $k => $v) {
    $udbList[$k] = explode(',', str_replace(["\r", "\n"], '', $v));//[[udb,yyuid], ...]
}
//$udbList = $GLOBALS['udbList'];
@$chunkSize = $_SERVER['argv'][2] ?: 3;//分片长度
$chunks = array_chunk($udbList, $chunkSize);
$allSummaryJsonFile = "{$tmpDir}/all_summary.json";//最终汇总json文件
$allSummaryFile = "{$tmpDir}/all_summary.csv";//最终汇总文件
$allSummaryFp = fopen($allSummaryFile, 'w+');

while (1) {
    $wait = false;    
    foreach ($chunks as $k => $v) {
        if (! file_exists("{$tmpDir}/{$k}/{$tmpDir}/finish.is")) $wait = true;
    }
    if ($wait) {
        echo "waiting for the result of calculate, has lasted for {$waitDur} s ...\r\n";
        sleep($sleepGap);
    } else {
        //todo: kpi.php已在其分程序目录的 $tmpDir 目录生成summary.json，循环收集一下，即可汇总
        $allSummary = [];
        foreach ($chunks as $k => $v) {
            @$chunkSummaryJson = file_get_contents("{$tmpDir}/{$k}/{$tmpDir}/summary.json") ?: '{}';
            $chunkSummary = json_decode($chunkSummaryJson, 1);//udb:{month:count}
            foreach ($chunkSummary as $udb => $monthlyCountMap) {
                foreach ($monthlyCountMap as $month => $count) {
                    $allSummary[$udb][$month] = $count;
                }
            }
        }
        
        //写入最终汇总标题行
        $titles = ['UDB', 'YYUID'];
        foreach ($monthList as $month) $titles[] = date('Y', strtotime($month.'01')).'年'.date('m', strtotime($month.'01')).'月';
        fputcsv($allSummaryFp, array_values($titles));
        
        //写入最终汇总数据
        foreach ($udbList as $udbData) {
            list ($udb, $yyuid) = $udbData;
            $monthlyCountMap = $allSummary[$udb];
            fputcsv($allSummaryFp, array_merge([$udb, $yyuid], array_values($monthlyCountMap)));
        }
        fclose($allSummaryFp);
        file_put_contents($allSummaryJsonFile, json_encode($allSummary, JSON_UNESCAPED_UNICODE));
        echo "\nAll finished!! Saved as {$allSummaryJsonFile}, {$allSummaryFile}\n";
        break;
    }
}

