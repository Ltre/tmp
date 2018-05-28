<?php

include 'hehe/config.php';
include 'hehe/lib.php';
include 'hehe/dwHttp.php';

class FinderStart {
    
    var $http;
    var $batch;
    
    function __construct($batch){
        $this->http = new dwHttp;
        $this->batch = $batch;
    }

    //[{"crc32val": "3677241848","domain_name": "英雄联盟","domain": "lol.duowan.com"},...]
    function getDomainList(){
        $url = 'http://comment3.duowan.com/index.php?r=mgr/getdomain';
        $ret = $this->http->get($url);
        $list = json_decode($ret?:'[]', 1);
        return $list;
    }
    
    
    //开启多个进程，一个域名占用一个
    function start(){
        $cmd = '';
        $dl = $this->getDomainList();
        foreach ($dl as $d) {
            $shabi = str_replace(['.duowan.com', '.5253.com'], '', $d['domain']);
            echo "\n nohup php comment-finder.php {$this->batch} scan 1 99999999 \"{$d['domain']}\" > comment-{$shabi}.nohup &";
        }
    }

}


@$batch = $_SERVER['argv'][1];//一般用20180528

(new FinderStart($batch))->start();

//php comment-finder-helper.php 20180528