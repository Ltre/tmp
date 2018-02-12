<?php

require_once 'config.php';
require_once 'Model.php';
require_once 'dwHttp.php';


$udbList = file("udblist.csv");

foreach ($udbList as $k => $v) {
    $udbList[$k] = explode(',', str_replace(["\r", "\n"], '', $v));//[[udb,yyuid], ...]
}


$monthList = $GLOBALS['monthList'];


function getVV($vid, $startDate, $endDate){
    $url = "http://61.147.186.105/?r=api/getAdPlay&startTime={$startDate}&endTime={$endDate}&vid={$vid}";
    $h = new dwHttp();
    $ret = $h->get($url, 20, "Host: playstats-manager.v.duowan.com");
    $json = json_decode($ret, true);
    return $json['result']['list']['byDate'];        
}


//分文件计算
$kpiDir = @$_SERVER['argv'][1] ?: date('YmdHis');
@mkdir($kpiDir, 0777, true);
$summaryData = [];//汇总数据, 第一层用udb，第二层用月份
$videoModel = new Model('upload_list', 'mysql_video');
foreach ($udbList as $udbData) {
    list ($udb, $yyuid) = $udbData;
    $udbDir = "{$kpiDir}/{$udb}";
    @mkdir($udbDir, 0777, true);
    //■■■■■■■■■■■■■■■Debug
    print_r("================================================\n");
    print_r('Created dir: '.$udbDir.', yyuid = '.$yyuid."\n");
    //■■■■■■■■■■■■■■■Debug
    foreach ($monthList as $month) {
        $monthFile = "{$udbDir}/{$month}.csv";//每个UDB每个月份对应文件
        @unlink($monthFile);
        $monthFp = fopen($monthFile, 'w+');
        //■■■■■■■■■■■■■■■Debug
        print_r('Created file: '.$monthFile."\n");
        //■■■■■■■■■■■■■■■Debug
        
        $titles = ['VID'];
        $startDate = $month.'01';
        $endDate = date('Ymd', strtotime($month.'01 +1 month -1 day'));
        for ($date = $startDate; $date <= $endDate; ) {
            $titles[] = $date;
            ++ $date;
        }
        
        fputcsv($monthFp, $titles);//写入标题行
        //■■■■■■■■■■■■■■■Debug
        print_r("Wrote titles: \n");
        print_r($titles);
        echo "\n";
        //■■■■■■■■■■■■■■■Debug
        
        $sql = "select vid from upload_list where yyuid=:yyuid order by vid desc";
        $vidQueryRs = $videoModel->query($sql, ['yyuid' => $yyuid]) ?: [];
        $vidsLen = count($vidQueryRs);
        //■■■■■■■■■■■■■■■Debug
        print_r('Vids\' count:'.count($vidQueryRs)."\n");
        //■■■■■■■■■■■■■■■Debug
        $playCountPerUDBMonth = 0;//每个UDB经过一个月，汇总一次
        foreach ($vidQueryRs as $k => $r) {
            $vid = $r['vid'];
            $vvMap = getVV($vid, $startDate, $endDate);//格式：[date] => ['load_num' => 0]
            //■■■■■■■■■■■■■■■Debug
            print_r('vvMap is:');
            print_r($vvMap);
            echo "\n";
            //■■■■■■■■■■■■■■■Debug
            $dataLine = [];
            foreach ($titles as $title) {
                if ($title == 'VID') {
                    $dataLine[] = $vid;
                } else {//Ymd日期对应的某视频播放数                    
                    $sum = (int) $vvMap[$title]['load_num'];
                    $dataLine[] = $sum;
                    $playCountPerUDBMonth += $sum;
                    //■■■■■■■■■■■■■■■Debug
                    print_r("[{$k}/{$vidsLen}] Current vid = {$vid}, title = {$title}, sum = {$sum} \n");
                    //■■■■■■■■■■■■■■■Debug
                }
            }
            fputcsv($monthFp, $dataLine);
        }
        $summaryData[$udb][$month] = $playCountPerUDBMonth;
        
        fclose($monthFp);
        file_put_contents($monthFile, iconv('UTF-8', 'GBK', file_get_contents($monthFile)));
    }
}

file_put_contents("{$kpiDir}/summary.json", json_encode($summaryData));//保存汇总文件
file_put_contents("{$kpiDir}/finish.is", '1');//标记任务完成
