<?php

require_once 'config.php';
require_once 'Model.php';
require_once 'dwHttp.php';

$bbList = [
    '�պض�' => ['dw_suhedong', '50031151'],
    '������' => ['dw_zengwanqiong1', '50074517'],
    '��ѧ��' => ['dw_fengxuegan', '50000334'],
    '�趬��' => ['dw_lidongkai', '50016778'],
    '�����' => ['dw_wangjunji', '50000373'],
    '���' => ['dw_zhaotao', '50004058'],
    '������' => ['dw_wuzihao', '50075833'],
    '��־' => ['dw_chenjiazhi', '50000537'],
    '������' => ['dw_panwenxu', '50015313'],
    '��־��' => ['dw_yuzhifang', '50000101'],
    '�����' => ['dw_huangruizi', '50075687'],
    '�ͼ���' => ['dw_laojianming', '50075755'],
    '�ֽ���' => ['dw_linjianming', '50075759'],
    '���ε�' => ['dw_zhangmengdi', '50015629'],
    '�³Ϲ�' => ['dw_chengguang', '50074688'],
    '��ʢ��' => ['dw_dongshengjie', '50016114'],
    '����' => ['dw_wupeng', '50000063'],
    '�ƺ�' => ['dw_huanghao1', '50040007'],
    '�⽨��' => ['dw_wujianqing', '50040004'],
    'ŷ��͢' => ['dw_ouzhenting', '50000336'],
    '����' => ['dw_gongheng', '50013396'],
    '�����' => ['dw_zhangyufeng', '50013910'],
    '������' => ['dw_liangyaoqi', '50002383'],
    '���' => ['dw_fengjun', '50000095'],
    '����' => ['dw_yuyang3', '50031135'],
    '����ϲ' => ['dw_mingchuanxi', '50031160'],
    '������' => ['dw_zhongyuxin', '734981673'],
];

$monthList = [
    '201612',
    '201701',
    '201702',
    '201703',
    '201704',
    '201705',
    '201706',
    '201707',
    '201708',
    '201709',
    '201710',
    '201711',
];


function getVV($vid, $startDate, $endDate){
    $url = "http://61.147.186.105/?r=api/getAdPlay&startTime={$startDate}&endTime={$endDate}&vid={$vid}";
    $h = new dwHttp();
    $h->get($url, 20, "Host: playstats-manager.v.duowan.com");
    $json = json_decode($ret, true);
    return $json['result']['list']['byDate'];        
}


//���ļ�����
$kpiDir = 'kpi_'.date('YmdHis');
@mkdir($kpiDir, 0777, true);
$videoModel = new Model('upload_list', 'mysql_video');
foreach ($bbList as $name => $items) {
    list ($udb, $yyuid) = $items;
    $udbDir = "{$kpiDir}/{$udb}";
    @mkdir($udbDir, 0777, true);
    //������������������������������Debug
    print_r("================================================\n");
    print_r('Created dir: '.$udbDir.', yyuid = '.$yyuid."\n");
    //������������������������������Debug
    foreach ($monthList as $month) {
        $monthFile = "{$udbDir}/{$month}.csv";
        @unlink($monthFile);
        $monthFp = fopen($monthFile, 'w+');
        //������������������������������Debug
        print_r('Created file: '.$monthFile."\n");
        //������������������������������Debug
        
        $titles = ['VID'];
        $startDate = $month.'01';
        $endDate = date('Ymd', strtotime($month.'01 +1 month -1 day'));
        for ($date = $startDate; $date <= $endDate; ) {
            $titles[] = $date;
            ++ $date;
        }
        
        fputcsv($monthFp, $titles);//д�������
        //������������������������������Debug
        print_r("Wrote titles: \n");
        print_r($titles);
        echo "\n";
        //������������������������������Debug
        
        //$sql = "select vid from upload_list where yyuid=:yyuid and can_play=1 and status!=-9 and upload_start_time < :endTime order by vid desc";
        //$sql = "select vid from upload_list where yyuid=:yyuid and upload_start_time < :endTime order by vid desc";
        $sql = "select vid from upload_list where yyuid=:yyuid order by vid desc";
        //$vidQueryRs = $videoModel->query($sql, ['yyuid' => $yyuid, 'endTime' => strtotime($month.'01 +1 month')]) ?: [];
        $vidQueryRs = $videoModel->query($sql, ['yyuid' => $yyuid]) ?: [];
        $vidsLen = count($vidQueryRs);
        //$statsTable = "stats_video_play_{$month}";
        //$statsModel = new Model($statsTable, 'mysql');
        //������������������������������Debug
        print_r('Vids\' count:'.count($vidQueryRs)."\n");
        //������������������������������Debug
        foreach ($vidQueryRs as $k => $r) {
            $vid = $r['vid'];
            $vvMap = getVV($vid, $startDate, $endDate);//��ʽ��[date] => ['load_num' => 0]
            //������������������������������Debug
            print_r('vvMap is:');
            print_r($vvMap);
            echo "\n";
            //������������������������������Debug
            $dataLine = [];
            foreach ($titles as $title) {
                if ($title == 'VID') {
                    $dataLine[] = $vid;
                } else {//Ymd���ڶ�Ӧ��ĳ��Ƶ������                    
                    //$sql = "select sum(load_num) s from {$statsTable} where vid = :vid and date_hour like :date";
                    //$sumQueryRs = $statsModel->query($sql, ['vid' => $vid, 'date' => "{$date}%"]);
                    //$sum = (int) $sumQueryRs[0]['s'];
                    $sum = (int) $vvMap[$title]['load_num'];
                    $dataLine[] = $sum;
                    //������������������������������Debug
                    print_r("[{$k}/{$vidsLen}] Current vid = {$vid}, title = {$title}, sum = {$sum} \n");
                    //������������������������������Debug
                }
            }
            fputcsv($monthFp, $dataLine);
        }
        
        fclose($monthFp);
    }
}

die;//������ͣ�����ԣ�


//���¿�ʼ����

$file = 'kpi.csv';
@unlink($file);
$fp = fopen($file, 'w+');
$titles = array(
    'THIS_IS_NAME' => '����',
    'THIS_IS_UDB' => 'UDB',
    'THIS_IS_YYUID' => 'YYUID',
);
foreach ($monthList as $month) $titles[$month] = date('Y', strtotime($month.'01')).'��'.date('m', strtotime($month.'01')).'��';

fputcsv($fp, array_values($titles));

foreach ($bbList as $name => $items) {
    list ($udb, $yyuid) = $items;
    $dataLine = [];
    foreach ($titles as $k => $v) {
        if ($k == 'THIS_IS_NAME') {
            $dataLine[] = $name;
        } elseif ($k == 'THIS_IS_UDB') {
            $dataLine[] = $udb;
        } elseif ($k == 'THIS_IS_YYUID') {
            $dataLine[] = $yyuid;
        } else {//���������·�
            $table = "stats_video_play_{$k}";
            $sql = "select sum(load_num) s from {$table} where channel = '{$channel}'";
            $m = new Model($table, 'mysql');
            $rs = $m->query($sql);
            $dataLine[] = $sum = (int) @$rs[0]['s'];
            echo "writing SUM = {$sum} where channel = {$channel} and month = {$k} ... \n";
        }
    }
    fputcsv($fp, $dataLine);
}

fclose($fp);