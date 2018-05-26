<?php

include 'hehe/config.php';
include 'hehe/lib.php';
include 'hehe/dwHttp.php';

class FinderStart {
    
    var $http;
    
    function __construct(){
        $this->http = new dwHttp;
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
        __mkdirs("cache");
        $dl = $this->getDomainList();
        foreach ($dl as $d) {
            $domain = $d['domain'];
            $shabi = str_replace(['.duowan.com', '.5253.com'], '', $d['domain']);
            //$cmd = "start \"cmt {$shabi} 1~\" php comment-finder.php \"1\" \"99999999\" \"{$d['domain']}\" ";
            //$cmdFile = "comment-finder-{$d['domain']}-cmd.bat";
            //file_put_contents("cache/{$cmdFile}", $cmd);
            $cmd .= "start \"cmt {$shabi} 1~\" php comment-finder.php \"1\" \"99999999\" \"{$d['domain']}\" \r\n";
        }
        file_put_contents('cache/comment-finder-multi.bat', $cmd);
    }

}


(new FinderStart)->start();

