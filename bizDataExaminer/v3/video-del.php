#!/usr/local/php/bin/php
<?php

include 'hehe/config.php';
include 'hehe/lib.php';
include 'hehe/dwHttp.php';
include 'hehe/Model.php';

class Del {
    
    var $batch;
    var $source;
    var $kwFields;
    var $d;
    var $http;
    var $kws;
    var $sqlite;
    
    
    function __construct($source, $batch){
        $this->batch = $batch;
        $this->source = $source;
        $this->kwFields = $GLOBALS['sources'][$source]['keywordFields'];
        $this->d = $GLOBALS['sources'][$source]['d'];
        $this->http = new dwHttp;
        $this->kws = file('hehe/kws.txt');
        array_walk($this->kws, function(&$v, $k){$v = trim($v);});
        $this->initDB($source, $batch);
    }
    
    
    function initDB($source, $batch){
        @mkdir('collect');
        $this->sqlite = new Model("collect/{$source}-{$batch}.db", 'video');
        $this->sqlite->query("create table if not exists video(
            vid bigint primary key,
            channel varchar(28),
            user_id bigint,
            title varchar(255),
            subtitle varchar(255),
            intro text,
            keywords varchar(255))");
    }
    
    
    function req($id){    
        $ret = $this->http->post($this->d, ['id' => $id], 55);
    }
    
  
    
    function run(){
        //var_dump($this->sqlite->query("SELECT COUNT(1) FROM video"));
        //print_r($this->sqlite->query("select count(1),channel from video where keywords like :kws group by channel", ['kws' => '%杀神%']));
        //print_r($this->sqlite->query("select * from video order by vid desc limit 50"));
    }
    
    
    function sql(){
        
    }
    
}

@$batch = $_SERVER['argv'][1];
@func = $_SERVER['argv'][2];
(new Del('video', $batch))->run();