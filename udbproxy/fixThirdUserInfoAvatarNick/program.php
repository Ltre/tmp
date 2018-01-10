#!/usr/local/php/bin/php
<?php

include __DIR__.'/config.php';
include __DIR__.'/Model.php';
include __DIR__.'/SeniorModel.php';


function updateOne($v){
    global $global;
    $data = json_decode($v['json_data']?:[], 1);
    echo "json_data is 【{$v['json_data']}】\n";
    print_r($data);
    echo "\n";
    if (empty($data)) return;
    $nickname = $avatar = '';
    switch ($v['type']) {
        case 'weixin':
            $nickname = $data['nickname'];
            $avatar = $data['headimgurl'];
            break;
        case 'qq':
            $nickname = $data['nickname'] ?: '';
            $avatar = $data['figureurl_2'] ?: $data['figureurl_1'] ?: $data['figureurl'] ?: $data['figureurl_qq_2'] ?: $data['figureurl_qq_1'];
            break;
        case 'weibo':
            $nickname = $data['name'] ?: $data['screen_name'];
            $avatar = $data['avatar_large'] ?: $data['avatar_hd'] ?: $data['profile_image_url'] ?: $data['cover_image_phone'];
            break;
        case 'tel':
            break;
        case 'mp':
            $nickname = $data['nick'];
            $avatar = $data['avatar'];
            break;
    }
    $tableName = getCurrTableName($global['tableN'], 1);
    $model = new SeniorModel($tableName, $global['mysqlInstance']);
    echo "tableName={$tableName}, p={$global['p']}, openid={$v['openid']}, type={$v['type']}, nickname={$nickname}, avatar={$avatar} \r\n";
    if ('' !== $nickname || '' !== $avatar) {
        $model->update(['openid' => $v['openid'], 'type' => $v['type']], [
            'nickname' => $nickname,
            'avatar' => $avatar,
        ]);
        echo "UPDATED!! \n";
    } else {
        echo "No nessary! \n";
    }
}


function update($tableN = 0, $p = 1, $limit = 100) {
    global $global;
    $p = getCurrP($p);
    $tableName = getCurrTableName($tableN, 1);
    $model = new SeniorModel($tableName, $global['mysqlInstance']);
    $ret = $model->seniorSelect([
        'select' => '*',
        'from' => $tableName,
        'where' => [
            'AND',
            [
                'OR',
                ['nickname', '=', ''],
                ['avatar', '=', ''],
            ],
            ['json_data', '!=', ''],
        ],
        'limitBy' => [$p, $limit, 10],
        'listable' => true,
        'pageable' => true,
    ]);
    
    echo "chunk ret is: \n";
    print_r($ret);
    echo "\n";

    $list = $ret['list'];
    $pages = $ret['pages'];
    
    print_r(['tableName' => $tableName, 'p' => $p, 'total_page' => $pages['total_page']]);
    echo "\n";
    
    foreach ($list as $v) {
        updateOne($v);
    }
    setNextP( $pages['total_page'] <= $p );
    
}


function setCache($k, $v){
    global $global;
    @mkdir($global['tmpdir'].'/'.$global['taskId'], 0777, true);
    $f = $global['tmpdir'].'/'.$global['taskId'].'/'.$k;
    file_put_contents($f, serialize($v));
}


function getCache($k) {
    global $global;
    $f = $global['tmpdir'].'/'.$global['taskId'].'/'.$k;
    if (! file_exists($f)) {
        return false;
    }
    return unserialize(file_get_contents($f));
}


//获取当前页码
function getCurrP($_p){
    global $global;
    $p = getCache('p');
    if (false === $p) {
        $p = $_p;
        setCache('p', $p);
    }
    $global['p'] = $p;
    return $p;
}


//获取下一页码
function setNextP($isLastP = false){
    global $global;
    if (! $isLastP) {
        ++ $global['p'];
    } else {
        $global['p'] = 1;
        setNextTableName();
    }
    setCache('p', $global['p']);
}


//获取当前表名
function getCurrTableName($tableStartNum, $fullNumLen = 3){
    global $global;
    $n = intval(@file_get_contents('tableN')?:0);

    //■■■■■■■■■■■■■■■■■■■DEBUG
    // $n = 0;//固定在某个表进行测试
    //■■■■■■■■■■■■■■■■■■■DEBUG

    if (false === $n) {
        $n = $tableStartNum;
        setCache('tableN', $n);
    }
    $global['tableN'] = $n;
    $len = strlen($n);
    if ($len < $fullNumLen) {
        $n = str_repeat('0', $fullNumLen-$len) . $n;
    }
    return $global['tableBaseName'] . '_' . $n;
}


//设置下一个表名
function setNextTableName(){
    global $global;
    if ($global['tableN'] < 999) {
        ++ $global['tableN'];
    } else {
        $global['tableN'] = 0;
    }
    setCache('tableN', $global['tableN']);
}


$global = [
    'taskId' => 'fuxk', //多任务区分ID
    'tableN' => 0, //当前进度表名
    'p' => 1, //当前进度页码
    'limit' => 100, //每limit条数据一轮
    'usleep' => 200, //每轮暂停usleep毫秒
    'tableBaseName' => 'third_userinfo',
    'mysqlInstance' => 'mysql',
    'tmpdir' => 'tmp',
];

//--------------------------------------
$global['taskId'] = $_SERVER['argv'][1] ?: $global['taskId'];
$global['tableN'] = $_SERVER['argv'][2] ?: $global['tableN'];
$global['p'] = $_SERVER['argv'][3] ?: $global['p'];

while (1) {
    update($global['tableN'], $global['p'], $global['limit']);
    usleep(100);
}
