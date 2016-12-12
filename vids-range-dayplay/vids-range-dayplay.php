<?php
/**
 * 根据VID列表，导出时间段内每日的播放量
 * 本文件必须保存为GB2312编码
 */
date_default_timezone_set('PRC');
ini_set("display_errors", "On");
error_reporting(E_ALL & ~E_STRICT);

mb_internal_encoding("GB2312");//确保导出文件支持WIN的中文环境


define('START_TIME', 20160801);
define('END_TIME', 20160828);
define('GET_VIDS_URL', "http://v.huya.com/?r=test/GetVidByUid&uid=");
define('GET_VV_URL', "http://playstats-manager.v.duowan.com/?r=api/getByVidToDate&startTime=".START_TIME."&endTime=".END_TIME."&vid="); 



function getVV($vid){
    static $static;
    if( !isset($static[$vid]) ){
        $ret = curlGet(GET_VV_URL.$vid);
        $json = json_decode($ret, true);
        $static[$vid] = $json['result']['list'];        
    }
    return $static[$vid];
}

function curlGet($url, $timeout=10) {
    do{
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $result = curl_exec($ch);        
        curl_close($ch);
    }while( empty($result) );
    
    return $result;
}


$vids = file("vids.txt");
$output = "123.csv"; 
$count = count($vids);
$num = 0;
@unlink($output);
foreach($vids as $vid){
    $vid = trim($vid);
    $num++;
    echo "vid:{$vid} {$num}/{$count}\r\n";
    $ret = @curlGet(GET_VV_URL.$vid);
    $json = json_decode($ret, true);
    
    $vv = getVV($vid);
    $data = array();
    $data['vid'] = $vid;
    $vidTotal = 0;    
    for($i=START_TIME; $i<=END_TIME; $i++){
        $data[$i]= isset($vv[$i]['load_num']) ? $vv[$i]['load_num'] : 0;
        $vidTotal += $data[$i];
    }
    $data['vidTotal'] = $vidTotal;
    
    $str = implode(',', $data)."\r\n";
    file_put_contents($output, $str, FILE_APPEND);
}
