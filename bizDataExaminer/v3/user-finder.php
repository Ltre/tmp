#!/usr/local/php/bin/php
<?php

include 'hehe/config.php';
include 'hehe/lib.php';
include 'hehe/dwHttp.php';
include 'hehe/Model.php';

class Finder {
    
    var $batch;
    var $source;
    var $kwFields;
    var $getter;
    var $http;
    var $kws;
    var $sqlite;
    
    
    function __construct($source, $batch, $kwsFileName='hehe/kws.txt'){
        $this->batch = $batch;
        $this->source = $source;
        $this->kwFields = $GLOBALS['sources'][$source]['keywordFields'];
        $this->getter = $GLOBALS['sources'][$source]['list_api'];
        $this->http = new dwHttp;
        $this->kws = file($kwsFileName);
        array_walk($this->kws, function(&$v, $k){$v = trim($v);});
        $this->initDB($source, $batch);
    }
    
    
    function initDB($source, $batch){
        @mkdir('collect');
        $this->sqlite = new Model("collect/{$source}-{$batch}.db", $source);
        $this->sqlite->query("create table if not exists user(
            user_id bigint primary key,
            udb varchar(30),
            nickname varchar(50),
            user_intro varchar(255),
            keywords varchar(255))");
    }
    
    
    function req($id, $limit){    
        $ret = $this->http->post($this->getter, ['id' => $id, 'limit' => $limit], 55);
        $list = json_decode($ret?:'[]', 1);
        echo 'get list count: '.count($list)."\r\n";
        $lastId = 9223372036854775807;
        foreach ($list as $v) {
            if (is_array($v)) {
                $lastId = $v['id'];
                echo 'foreach by id: '.$v['id']."\r\n";
                $foundKws = [];
                foreach ($this->kws as $kw) {
                    foreach ($this->kwFields as $field) {
                        echo 'foreach by id: '.$v['id'].', by field: '.$field."\r\n";
                        if (isset($v[$field]) && '' !== $kw && false !== mb_strpos($v[$field], $kw)) {
                            in_array($kw, $foundKws) OR $foundKws[] = $kw;
                            continue 2;
                        }
                    }
                }
                if (count($foundKws) > 0) {
                    echo "Found id: {$v['id']}\r\n";
                    $this->collect($v, $foundKws);
                }
            }
        }
        return [$lastId, count($list)];
    }
    
    
    function collect($data, $foundKws){
        if (! $this->sqlite->find(['vid' => $data['vid']])) {            
            $data = [
                'user_id' => $data['user_id'],
                'udb' => $data['udb'],
                'nickname' => $data['nickname'],
                'keywords' => join(',', $foundKws),
            ];
            $this->sqlite->insert($data);
            $this->log('collect', print_r($data, 1));
        }
    }
    
    
    function log($name, $content){
        @mkdir('log');
        file_put_contents("log/{$this->source}-{$this->batch}-{$name}.log", $content."\n", FILE_APPEND);
    }
    
    
    //@todo 执行到某行时出错：Found id: 549762506740
        //PHP Fatal error:  Call to a member function bindParam() on a non-object in /home/fangkunbiao/bizDataExaminer/hehe/Model.php on line 153
    //以后再修
    function scan($lastId = 9223372036854775807, $minId = 1){
        $limit = 1000;
        while ($lastId > $minId) {
            echo "lastId: {$lastId}\n";
            list($lastId, $total) = $this->req($lastId, $limit);
            $this->log('scan', "lastId: {$lastId}");
        }
    }
    
    
    function sql($sql){
        $ret = print_r($this->sqlite->query($sql), 1);
        $this->log('sql', "sql: {$sql}\nret: {$ret}");
        echo $ret;
    }
    
    
    function del(){
        $d = $GLOBALS['sources'][$this->source]['d'];
        $list = $this->sqlite->query("select * from user ");
        foreach ($list as $v) {
            $ret = $this->http->post(ltredc($d), ['vid' => $vid], 55);
            $this->log('del', "vid: {$v['vid']}, ret: {$ret}");
            echo $ret."\n";
        }
    }
    
    function test(){
        print_r($this->sqlite->query("select * from user limit 10"));
    }
    
}

@$batch = $_SERVER['argv'][1];//一般用20180528
@$func = $_SERVER['argv'][2];
@$param1 = $_SERVER['argv'][3];
@$param2 = $_SERVER['argv'][4];
@$param3 = $_SERVER['argv'][5] ?: 'hehe/kws.txt';

switch ($func) {
    case 'scan':
        $params = [$param1?:1, $param2?:8871709];//lastId, $maxId
        break;
    case 'sql':
        $params = [$param1];
        break;
    case 'del':
        $params = [];
        break;
    case 'test':
        $params = [];
        break;
    default:
        die('wtf');
}

if ($func) {    
    $finder = new Finder('user', $batch, $param3);
    call_user_func_array([$finder, $func], $params);
}

//example: /usr/local/php/bin/php user-finder.php 20180528 scan 9223372036854775807 1 "hehe/kws20190124.txt"
//example: /usr/local/php/bin/php user-finder.php 20180528 sql "select count(1),keywords from user group by keywords"
//example: /usr/local/php/bin/php user-finder.php 20180528 del

//导出到HTML的操作：
//先执行: /usr/local/php/bin/php user-finder.php 20180528 sql "select *, 'http://cloud.v.duowan.com/index.php?r=public/play&vid='||vid as backurl, 'http://video.duowan.com/play/'||vid||'.html' as userurl from user" > user.export.20190124-1.txt
//在notepad++中用正则将链接替换成超链接标签
//  http\:\/\/user\.duowan\.com\/u\/\d+  ->  <a target="_blank" href="$0">$0</a>
