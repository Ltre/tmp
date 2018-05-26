<?php

include 'hehe/config.php';
include 'hehe/lib.php';
include 'hehe/dwHttp.php';

class Finder {
    
    var $batch;
    var $source;
    var $kwFields;
    var $getter;
    var $http;
    var $kws;
    
    
    function __construct($source, $batch){
        $this->batch = $batch;
        $this->source = $source;
        $this->kwFields = $GLOBALS['sources'][$source]['keywordFields'];
        $this->getter = $GLOBALS['sources'][$source]['list_api'];
        $this->http = new dwHttp;
        $this->kws = file('hehe/kws.txt');
    }
    
    
    function req($id, $limit){    
        $ret = $this->http->post($this->getter, ['id' => $id, 'limit' => $limit], 55);
        $list = json_decode($ret?:'[]', 1);
        echo 'get list count: '.count($list)."\r\n";
        $lastId = 1;
        foreach ($list as $v) {
            $lastId = $v['id'];
            //echo 'foreach by id: '.$v['id']."\r\n";
            if (is_array($v)) {
                foreach ($this->kwFields as $field) {
                    //echo 'foreach by id: '.$v['id'].', by field: '.$field."\r\n";
                    if (! isset($v[$field])) continue;
                    //echo 'found by field: '.$field."\r\n";
                    foreach ($this->kws as $kw) {
                        //echo 'foreach by id: '.$v['id'].', by field: '.$field.', by kw: '.$kw."\r\n";
                        //echo 'compare result: '.(false!==mb_strpos($v[$field], $kw)?'FOUND!':'not found.')."\r\n";
                        $kw = trim($kw);
                        if ('' !== $kw && false !== mb_strpos($v[$field], $kw)) {
                            //print_r($v);
                            echo "Found id: {$v['id']}\r\n";
                            $this->collect($v['id'], $v, $this->source.'-'.$this->batch);
                            continue 3;
                        }
                    }
                }
            }
        }
        return $lastId;
    }
    
    
    function collect($id, $data, $filePre){
        file_put_contents("collect/{$filePre}-list.txt", print_r($data, 1)."\r\n", FILE_APPEND);
        file_put_contents("collect/{$filePre}-ids.txt", $id."\n", FILE_APPEND);
    }
    
    
    function run($lastId = 1, $maxId = 8871709){
        while ($lastId != $maxId) {
            $lastId = $this->req($lastId, 1000);
        }    
    }
    
}

@$lastId = $_SERVER['argv'][1] ?: 1;
@$maxId = $_SERVER['argv'][2] ?: 8871709;
$batch = date('Ymd-His').'-'."{$lastId}_to_{$maxId}";
(new Finder('tu', $batch))->run($lastId, $maxId);