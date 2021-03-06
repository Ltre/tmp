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
        $this->sqlite->query("create table if not exists comment(
            id bigint primary key,
            content text,
            domain varchar(50),
            keywords varchar(255))");
    }
    
    
    function req($id, $limit, $domain){
        $ret = $this->http->post($this->getter, ['id' => $id, 'limit' => $limit, 'domain' => $domain], 55);
        $list = json_decode($ret?:'[]', 1);
        if (false === $list && is_string($ret)) {            
            $this->log('reqerr-domain-'.$domain, 'req: '.print_r(['id' => $id, 'limit' => $limit, 'tb_id' => $tb_id], 1)."\nresponse: {$ret}");
            return [$id, $limit];//请求出错，返回404页面HTML，这里作重试处理
        }
        echo 'get list count: '.count($list)."\r\n";
        $lastId = 0;
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
                    $this->collect($domain, $v, $foundKws);
                }
            }
        }
        return [$lastId, count($list)];
    }
    
    
    function collect($domain, $data, $foundKws){
        if (! $this->sqlite->find(['id' => $data['id']])) {            
            $data = [
                'id' => $data['id'],
                'content' => $data['content'],
                'domain' => $domain,
                'keywords' => join(',', $foundKws),
            ];
            $this->sqlite->insert($data);
            $this->log('collect-'.$domain, print_r($data, 1));
        }
    }
    
    
    function log($name, $content){
        @mkdir('log');
        file_put_contents("log/{$this->source}-{$this->batch}-{$name}.log", $content."\n", FILE_APPEND);
    }
    
    
    function scan($lastId = 0, $maxId = 99999999, $domain = 'tu.duowan.com'){
        $limit = 1000;
        $total = $limit;
        while ($total == $limit) {
            list($lastId, $total) = $this->req($lastId, $limit, $domain);
            $this->log('scan-'.$domain, "lastId: {$lastId}, limit: {$limit}, domain: {$domain}");
        }
    }
    
    
    function sql($sql){
        $ret = print_r($this->sqlite->query($sql), 1);
        $this->log('sql', "sql: {$sql}\nret: {$ret}");
        echo $ret;
    }
    
    
    function del(){
        $d = $GLOBALS['sources'][$this->source]['d'];
        $list = $this->sqlite->query("select * from comment where keywords!='杀人' and keywords !='杀神' and keywords!='杀人,杀神' and keywords!='杀神,杀人'");
        foreach ($list as $v) {
            $ret = $this->http->post(ltredc($d), ['dels' => $v['id'], 'domain' => $v['domain'], 'sign' => md5("comment3{$v['id']}")], 55);
            $log = "id: {$v['id']}, domain: {$v['domain']}, sign: ".md5("comment3{$v['id']}").",ret: {$ret}";
            $this->log('del', $log);
            echo $log."\n";
        }
    }
    
    function test(){
        print_r($this->sqlite->query("select * from comment limit 10"));
    }
    
}

@$batch = $_SERVER['argv'][1];//一般用20180528
@$func = $_SERVER['argv'][2];
@$param1 = $_SERVER['argv'][3];
@$param2 = $_SERVER['argv'][4];
@$param3 = $_SERVER['argv'][5];

switch ($func) {
    case 'scan':
        $params = [$param1?:0, $param2?:99999999, $param3?:'tu.duowan.com'];//lastId, $maxId, $domain
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
    $finder = new Finder('comment', $batch);
    call_user_func_array([$finder, $func], $params);
}

// /usr/local/php/bin/php
//example: php comment-finder.php 20180528 scan 0 99999999 "tu.duowan.com"
//example: /usr/local/php/bin/php comment-finder.php 20180528 sql "select count(1),domain from comment group by domain"
// /usr/local/php/bin/php comment-finder.php 20180528 sql "select count(1),keywords from comment where domain = 'tu.duowan.com' and keywords!='杀人' and keywords !='杀神' and keywords!='杀人,杀神' and keywords!='杀神,杀人' group by keywords"
//example: /usr/local/php/bin/php comment-finder.php 20180528 del
