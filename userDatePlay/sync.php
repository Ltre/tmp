<?php

//要求可循环整个日期段，且多次同步同一个日期，不会造成重复增量
//方法：当同步某用户在日期 MMDD 的量时，可设定一个临时增量器，在一次循环中初次进入该日期则清零；
    //待此用户在该日期数据同步满后，将该临时增量器的值赋值到此用户在该日期对应的播放总和。

function syncByUid($uid, $start, $end){
    $vids = getVidsByUid($uid);
    
}


//先获取所有数据源，之后每次调用该方法时仅取一部分分片，直至消耗完毕；消耗完毕后，下次则获取下一个用户的数据源。
function getVidsByUid($uid){
    static $chunkId = 0;//这里在duowanvideo改造为cache方式获取
    $source = [
        123, 234, 345, 456, 567, 678, 789, 890, 901,
        1123, 1234, 1345, 1456, 1567, 1678, 1789, 1890, 1901,
        2123, 2234, 2345, 2456, 2567, 2678, 2789, 2890, 2901,
    ];
    $chunks = array_chunk($source, 500);
    @$ret = $chunks[$chunkId] ?: [];
    $chunkId = $chunkId + 1 < count($chunks) ? ($chunkId + 1) : 0;//这里在duowanvideo改造为cache方式保存
    return $ret;
}


function saveToDate(array $vids, $start, $end){
    $apiRet = 
    $rs = [];
    for ($date = $start; $date <= $end; $date = date('Ymd', strtotime($date . ' +1 day'))) {

    }
    return [

    ];
}


function getSumToDate(array $vids, $start, $end){
    return [
        '20160901' => ['loadNum' => 12, 'adNum' => 2],
        '20160903' => ['loadNum' => 12, 'adNum' => 2],
        '20160907' => ['loadNum' => 12, 'adNum' => 2],
        '20160908' => ['loadNum' => 12, 'adNum' => 2],
    ];
}
