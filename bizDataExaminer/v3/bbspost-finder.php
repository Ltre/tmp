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
    
    
    function __construct($source, $batch){
        $this->batch = $batch;
        $this->source = $source;
        $this->kwFields = $GLOBALS['sources'][$source]['keywordFields'];
        $this->getter = $GLOBALS['sources'][$source]['list_api'];
        $this->http = new dwHttp;
        $this->kws = file('hehe/kws.txt');
        array_walk($this->kws, function(&$v, $k){$v = trim($v);});
        $this->initDB($source, $batch);
    }
    
    
    function initDB($source, $batch){
        @mkdir('collect');
        $this->sqlite = new Model("collect/{$source}-{$batch}.db", $source);
        $this->sqlite->query("create table if not exists bbspost(
            id bigint primary key,
            title varchar(255),
            content text,
            dateline varchar(50),
            keywords varchar(255))");
    }
    
    
    function req($id, $limit, $tb_id=0){    
        $ret = $this->http->post($this->getter, ['id' => $id, 'limit' => $limit, 'tb_id' => $tb_id], 55);
        $list = json_decode($ret?:'[]', 1);
        if (false === $list && is_string($ret)) {            
            $this->log('reqerr-tb-'.$tb_id, 'req: '.print_r(['id' => $id, 'limit' => $limit, 'tb_id' => $tb_id], 1)."\nresponse: {$ret}");
            return [$id, $limit];//请求出错，返回404页面HTML，这里作重试处理
        }
        echo 'get list count: '.count($list)."\r\n";
        $lastId = 1;
        foreach ($list as $v) {
            if (is_array($v)) {
                $lastId = $v['id'];
                //echo 'foreach by id: '.$v['id']."\r\n";
                $foundKws = [];
                foreach ($this->kws as $kw) {
                    foreach ($this->kwFields as $field) {
                        //echo 'foreach by id: '.$v['id'].', by field: '.$field."\r\n";
                        if (isset($v[$field]) && '' !== $kw && false !== mb_strpos($v[$field], $kw)) {
                            in_array($kw, $foundKws) OR $foundKws[] = $kw;
                            continue 2;
                        }
                    }
                }
                if (count($foundKws) > 0) {
                    echo "Found id: {$v['id']}\r\n";
                    $this->collect($tb_id, $v, $foundKws);
                }
            }
        }
        return [$lastId, count($list)];
    }
    
    
    function collect($tb_id, $data, $foundKws){
        if (! $this->sqlite->find(['id' => $data['id']])) {            
            $data = [
                'id' => $data['id'],
                'title' => $data['title'],
                'content' => $data['content'],
                'dateline' => $data['dateline'],
                'keywords' => join(',', $foundKws),
            ];
            $this->sqlite->insert($data);
            $this->log('collect-tb-'.$tb_id, print_r($data, 1));
        }
    }
    
    
    function log($name, $content){
        @mkdir('log');
        file_put_contents("log/{$this->source}-{$this->batch}-{$name}.log", $content."\n", FILE_APPEND);
    }
    
    
    function scan($lastId = 1, $maxId = 99999999, $tb_id = 0){
        $limit = 1000;
        $total = $limit;
        while ($total == $limit) {
            list($lastId, $total) = $this->req($lastId, $limit, $tb_id);
            $this->log('scan-tb-'.$tb_id, "lastId: {$lastId}, limit: {$limit}, lastId: {$lastId}");
        }
    }   
    
    
    function sql($sql){
        $ret = print_r($this->sqlite->query($sql), 1);
        $this->log('sql', "sql: {$sql}\nret: {$ret}");
        echo $ret;
    }
    
    
    function del(){
        $d = $GLOBALS['sources'][$this->source]['d'];
        $list = $this->sqlite->query("select * from bbspost");
        foreach ($list as $v) {
            $ret = $this->http->post(ltredc($d), ['pids' => $v['id'], 'sign' => md5("bbs{$v['id']}")], 55);
            $log = "id: {$v['id']}, sign: ".md5("bbs{$v['id']}").",ret: {$ret}";
            $this->log('del', $log);
            echo $log."\n";
        }
    }
    
    function test(){
        print_r($this->sqlite->query("select * from bbspost limit 10"));
    }
    
}

@$batch = $_SERVER['argv'][1];//一般用20180528
@$func = $_SERVER['argv'][2];
@$param1 = $_SERVER['argv'][3];
@$param2 = $_SERVER['argv'][4];
@$param3 = $_SERVER['argv'][5];

switch ($func) {
    case 'scan':
        $params = [$param1?:1, $param2?:99999999, $param3?:0];//lastId, $maxId, $tb_id(0~139)
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
    $finder = new Finder('bbspost', $batch);
    call_user_func_array([$finder, $func], $params);
}

//example: php bbspost-finder.php 20180528 scan 1 99999999 0
//example: php bbspost-finder.php 20180528 sql "select count(1) from bbspost"
//example: php bbspost-finder.php 20180528 del