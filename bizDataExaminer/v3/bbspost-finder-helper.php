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
    
    
    //开启多个进程，一个域名占用一个
    function start(){
        for ($i=0; $i<140; ++$i) {            
            echo "\n nohup php bbspost-finder.php {$this->batch} scan 0 99999999 {$i} > bbspost-tb-{$i}.nohup &";
        }
    }

}


@$batch = $_SERVER['argv'][1];//一般用20180528

(new FinderStart($batch))->start();

//php bbspost-finder-helper.php 20180528
// /usr/local/php/bin/php bbspost-finder-helper.php 20180528