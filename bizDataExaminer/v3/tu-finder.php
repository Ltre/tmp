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
        $this->sqlite->query("create table if not exists tu(
            id bigint primary key,
            title varchar(255),
            description text,
            keywords varchar(255))");
    }
    
    
    function req($id, $limit){    
        $ret = $this->http->post($this->getter, ['id' => $id, 'limit' => $limit], 55);
        $list = json_decode($ret?:'[]', 1);
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
                    $this->collect($v, $foundKws);
                }
            }
        }
        return [$lastId, count($list)];
    }
    
    
    function collect($data, $foundKws){
        if (! $this->sqlite->find(['id' => $data['id']])) {            
            $data = [
                'id' => $data['id'],
                'title' => $data['title'],
                'description' => $data['description'],
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
    
    
    function scan($lastId = 1, $maxId = 8871709){
        $limit = 1000;
        while ($lastId < $maxId) {
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
        $list = $this->sqlite->query("select * from tu");
        foreach ($list as $v) {
            $ret = $this->http->post(ltredc($d), ['id' => $v['id'], 'sign' => md5("tu{$v['id']}")], 55);
            $log = "id: {$v['id']}, sign: ".md5("tu{$v['id']}").",ret: {$ret}";
            $this->log('del', $log);
            echo $log."\n";
        }
    }
    
    function test(){
        print_r($this->sqlite->query("select * from tu limit 10"));
    }
    
}

@$batch = $_SERVER['argv'][1];//一般用20180528
@$func = $_SERVER['argv'][2];
@$param1 = $_SERVER['argv'][3];
@$param2 = $_SERVER['argv'][4];

switch ($func) {
    case 'scan':
        $params = [$param1?:1, $param2?:99999999];//lastId, $maxId
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
    $finder = new Finder('tu', $batch);
    call_user_func_array([$finder, $func], $params);
}

//example: php tu-finder.php 20180528 scan 1 99999999
//example: php tu-finder.php 20180528 sql "select count(1) from tu"
//example: php tu-finder.php 20180528 del
