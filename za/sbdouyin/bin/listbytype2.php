<?php

include 'config.php';
include 'Model.php';
include 'SQLiteModel.php';

//初始化： mkdir /tmp/pio_test; mkdir /tmp/pio_test/audio; mkdir /tmp/pio_test/cover; mkdir /tmp/pio_test/typecover; chmod -R 777 /tmp/pio_test; rm /tmp/pio_test/audio/* -rf; rm /tmp/pio_test/cover/* -rf; rm /tmp/pio_test/typecover/* -rf; rm /tmp/pio_test/log.log -f
//清理数据表： TRUNCATE mc_info; TRUNCATE mc_type; TRUNCATE mc_relate; 
//更新程序： rm /home/web/pio/sbdouyin/* -f; cd /home/web/pio/sbdouyin #之后拖放一次本目录下的所有php文件
//执行全部：  /usr/local/php/bin/php listbytype2.php
//执行单个归类：  nohup /usr/local/php/bin/php listbytype2.php 865 >> 865.nohup &
//仅在linux执行

class LuDouyin {

    var $pathPre;

    var $debug = 0;

    var $sqlite;

    function __construct(){
        $this->pathPre = $GLOBALS['resource'] ?: '/tmp/pio_test';
    }

    //新版入口：可指定一个分类，为多路程序提供支持
    function doFuckTypes($mc_id = 0){
        $totalTypes = [];
        do {
            @list ($succ, $msg, $resp, $types, $hasMore, $cursor) 
                = $this->getTypes($cursor);
            $this->log("--> doFuckTypes: cursor={$cursor}");//debug
            $this->log("result: succ={$succ}, msg={$msg}, types_count=".count($types).', hasMore= '.($hasMore?1:0).', new_cursor='.$cursor);//debug
            $succ || $this->log("result is failure, resp={$resp}");//debug
            if (! $succ) {
                throw new Exception($msg);
            }
            foreach ($types as $t) array_push($totalTypes, $t);
        } while ($hasMore);

        if ($mc_id) {
            foreach ($totalTypes as $type) {
                if ($type['mc_id'] == $mc_id) {
                    echo "@@@ mc_id={$mc_id}\n";//debug
                    $this->saveType($type);
                    $this->doFuckList($type['mc_id']);
                }
            }
        } else {
            foreach ($totalTypes as $type) {
                echo "### mc_id={$type['mc_id']}\n";//debug
                $this->saveType($type);
                $this->doFuckList($type['mc_id']);
            }
        }
    }


    //旧版入口：单线循环每个归类（效率低）
    function doFuckTypes_OLD(){
        do {
            @list ($succ, $msg, $resp, $types, $hasMore, $cursor) 
                = $this->getTypes($cursor);
            $this->log("--> doFuckTypes: cursor={$cursor}");//debug
            $this->log("result: succ={$succ}, msg={$msg}, types_count=".count($types).', hasMore= '.($hasMore?1:0).', new_cursor='.$cursor);//debug
            $succ || $this->log("result is failure, resp={$resp}");//debug
            if (! $succ) {
                throw new Exception($msg);
            }
            foreach ($types as $type) {
                $this->saveType($type);
                $this->doFuckList($type['mc_id']);
            }
        } while ($hasMore);
    }


    function getTypes($cursor = 0){
        @unlink('sbtypes.txt');
        shell_exec('curl -A "com.ss.android.ugc.aweme/530 (Linux; U; Android 8.0.0; zh_CN_#Hans; VTR-AL00; Build/HUAWEIVTR-AL00; Cronet/58.0.2991.0)" \
            -b "install_id=65343929505; ttreq=1$d7a1dd3c78fdfc6c0a392d01870aad0a90aad819; odin_tt=e08ae73c9ba20641a66a9b2fb888ea7a0672f38beecc0efe5e2f6205609428b7a5796455e568ea6c309147478477d9d7; sid_guard=0cf354f1237af5f7104349f9c0c516bc%7C1551865956%7C5184000%7CSun%2C+05-May-2019+09%3A52%3A36+GMT; uid_tt=4024e1833ba336fbf3c5c5f154356f61; sid_tt=0cf354f1237af5f7104349f9c0c516bc; sessionid=0cf354f1237af5f7104349f9c0c516bc; qh[360]=1" \
            -H "Host: api.amemv.com" \
            -H "Connection: keep-alive" \
            -H "X-SS-REQ-TICKET: 1552019091946" \
            -H "X-Tt-Token: 000cf354f1237af5f7104349f9c0c516bc935cfc3e394b34009e4ac65998088402b276f5eaa0fa2e9377e531e600bc02f44b" \
            -H "sdk-version: 1" \
            -H "X-Khronos: 1552019091" \
            -H "X-Gorgon: 030039908000263c8b5fc3e8db88ef6a6af0198d89d313a1ddc6" \
            -H "X-Pods: " \
            "https://api.amemv.com/aweme/v1/music/collection/?cursor='.$cursor.'&count=1024&ts=1552019092&js_sdk_version=1.10.4&app_type=normal&manifest_version_code=530&_rticket=1552019091954&ac=wifi&device_id=48912932681&iid=65343929505&mcc_mnc=46000&os_version=8.0.0&channel=huawei&version_code=530&device_type=VTR-AL00&language=zh&resolution=1080*1920&openudid=6b1e80386b74f00c&update_version_code=5302&app_name=aweme&version_name=5.3.0&os_api=26&device_brand=HUAWEI&ssmix=a&device_platform=android&dpi=480&aid=1128&as=a105cea864098c0e114166&cp=ec98c6584d1a8fefe1KySg&mas=01a2f1c4200aca73c3586e384cd0639b4d6c6c8c2c8c8ca60cc61c" \
        > sbtypes.txt');
        $resp = file_get_contents('sbtypes.txt');

        if (false === $resp || empty($resp)) {
            return [false, 'req types error', '', [], false];
        }
        $respData = json_decode($resp, 1);
        if (! isset($respData['mc_list']) || $respData['status_code'] != 0) {
            return [false, 'parse types error', $resp, [], false];
        }
        $hasMore = $respData['has_more'] == 1;
        $cursor = $respData['cursor'];
        $types = $respData['mc_list'];
        return [true, 'ok', $resp, $types, $hasMore, $cursor];
    }


    function saveType(array $type){
        $typeT = $this->table('mc_type');
        $find = $typeT->find(['type_id' => $type['mc_id']]);
        $path = $find ? $find['type_cover'] : $this->downloadTypeCover($type);
        $data = [
            'type_id' => $type['mc_id'],
            'type_name' => $type['mc_name'],
            'type_cover' => $path,
        ];
        if ($find) {
            $typeT->update(['type_id' => $type['mc_id']], $data);
        } else {
            $typeT->insert($data);
        }
    }


    //下载分类的封面
    function downloadTypeCover(array $type){
        $coverUrl = $type['cover']['url_list'][0] ?: $type['aweme_cover']['url_list'][0];
        $typeMap = [
            'image/jpeg' => 'jpg',
            'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/apng' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
        ];
        $h = get_headers($coverUrl, 1);
        $ext = $typeMap[$h['Content-Type']];
        $path = "typecover/{$type['mc_id']}.{$ext}";
        $c = file_get_contents($coverUrl);
        file_put_contents("{$this->pathPre}/{$path}", $c);
        $this->log("typecover path: {$path}\n");
        $this->log("typecover url: {$coverUrl}\n");
        $this->log("save typecover to: {$this->pathPre}/{$path}");
        return $path;
    }


    function getListByType($mc_id, $cursor = 0, $count = 30){
        @unlink('shabidouyin.txt');
        shell_exec('curl -A "com.ss.android.ugc.aweme/530 (Linux; U; Android 8.0.0; zh_CN_#Hans; VTR-AL00; Build/HUAWEIVTR-AL00; Cronet/58.0.2991.0)" \
            -b "install_id=65343929505; ttreq=1$d7a1dd3c78fdfc6c0a392d01870aad0a90aad819; odin_tt=e08ae73c9ba20641a66a9b2fb888ea7a0672f38beecc0efe5e2f6205609428b7a5796455e568ea6c309147478477d9d7; sid_guard=0cf354f1237af5f7104349f9c0c516bc%7C1551865956%7C5184000%7CSun%2C+05-May-2019+09%3A52%3A36+GMT; uid_tt=4024e1833ba336fbf3c5c5f154356f61; sid_tt=0cf354f1237af5f7104349f9c0c516bc; sessionid=0cf354f1237af5f7104349f9c0c516bc; qh[360]=1" \
            -H "Host: api.amemv.com" \
            -H "Connection: keep-alive" \
            -H "X-SS-REQ-TICKET: 1552123398554" \
            -H "X-Tt-Token: 000cf354f1237af5f7104349f9c0c516bc935cfc3e394b34009e4ac65998088402b276f5eaa0fa2e9377e531e600bc02f44b" \
            -H "sdk-version: 1" \
            -H "X-Khronos: 1552123398" \
            -H "X-Gorgon: 03006cc00000dc9a2db3a76ab9c9eccb135c4fc5dff8f97fbab1" \
            -H "X-Pods: " \
            "https://api.amemv.com/aweme/v1/music/list/?mc_id='.$mc_id.'&cursor='.$cursor.'&count='.$count.'&ts=1552123219&js_sdk_version=1.10.4&app_type=normal&manifest_version_code=530&_rticket=1552123219697&ac=wifi&device_id=48912932681&iid=65343929505&mcc_mnc=46000&os_version=8.0.0&channel=huawei&version_code=530&device_type=VTR-AL00&language=zh&resolution=1080*1920&openudid=6b1e80386b74f00c&update_version_code=5302&app_name=aweme&version_name=5.3.0&os_api=26&device_brand=HUAWEI&ssmix=a&device_platform=android&dpi=480&aid=1128&as=a115c87893752c35634388&cp=8f5ac25034398857e1OyWg&mas=01888c81175f386ffb492b1e8ac6d153c91c1ccc2ccc6cacccc64c" \
            > shabidouyin.txt');
        $resp = file_get_contents('shabidouyin.txt');

        if (false === $resp || empty($resp)) {
            return [false, 'req error', '', [], false];
        }
        $respData = json_decode($resp, 1);
        if (! isset($respData['music_list']) || $respData['status_code'] != 0) {
            return [false, 'parse error', $resp, [], false];
        }
        $list = $respData['music_list'];
        $hasMore = $respData['has_more'] == 1;
        $cursor = $respData['cursor'];
        return [true, 'ok', $resp, $list, $hasMore, $cursor];
    }



    function doFuckList($mc_id, $cursor = 0){
        do {
            @list ($succ, $msg, $resp, $list, $hasMore, $cursor) 
                = $this->getListByType($mc_id, $cursor, $hasMore);
            $this->log("--> getListByType: mc_id={$mc_id}, cursor={$cursor}");//debug
            $this->log("result: succ={$succ}, msg={$msg}, list_count=".count($list).', hasMore= '.($hasMore?1:0).', new_cursor='.$cursor);//debug
            $succ || $this->log("result is failure, resp={$resp}");//debug
            if (! $succ) {
                throw new Exception($msg);
            }
            $this->save($list, $mc_id);
        } while ($hasMore);
    }


    function save($list, $mc_id){
        $musicT = $this->table('mc_info');//@todo 新建一个库再跑
        foreach ($list as $v) {
            $find = $musicT->find(['mid' => $v['mid']]);
            $data = [
                'mid' => $v['mid'],
                'cover_thumb' => $find ? $find['cover_thumb'] : $this->downloadImg($v['mid'], 'thumb', $v['cover_thumb']['url_list'][0]),
                'cover_medium' => $find ? $find['cover_medium'] : $this->downloadImg($v['mid'], 'medium', $v['cover_medium']['url_list'][0]),
                'cover_large' => $find ? $find['cover_large'] : $this->downloadImg($v['mid'], 'large', $v['cover_large']['url_list'][0]),
                'cover_hd' => $find ? $find['cover_hd'] : $this->downloadImg($v['mid'], 'hd', $v['cover_hd']['url_list'][0]),
                'title' => $v['title'],
                'play_url' => $v['play_url']['url_list'][0],
                'duration' => $v['duration'],
                'save_path' => $find ? $find['save_path'] : $this->downloadMusic($v['mid'], $v['play_url']['url_list'][0]),
                'created' => time(),
            ];
            if ($find) {
                $this->log("update mc_info by mid={$v['mid']}");
                $musicT->update(['mid' => $v['mid']], $data);
            } else {
                $this->log("insert mc_info by mid={$v['mid']}");
                $musicT->insert($data);
            }

            $relate = [
                'type_id' => $mc_id,
                'mid' => $v['mid'],
            ];
            $relateT = $this->table('mc_relate');
            $relateData = $relateT->find($relate);
            if (! $relateT->find($relate)) {
                $relateT->insert($relate);
            }
        }
    }


    //保存音乐相对路径，方便迁移
    function downloadMusic($mid, $url){
        /* $typeMap = [
            'mpeg/mp3' => 'mp3',
            // '' => 'aac',
            // '' => 'ogg',
            // '' => 'm4a',
        ];
        $h = get_headers($url, 1);
        $ext = $typeMap[$h['Content-Type']]; */
        $ext = 'mp3';//链接实际返回document类型，故强制设置mp3
        $subdir = substr($mid, 0, 2);
        $midGuard = sha1($mid);
        $path = "audio/{$subdir}/{$midGuard}.{$ext}";
        @mkdir("{$this->pathPre}/audio/{$subdir}");
        $c = file_get_contents($url);
        file_put_contents("{$this->pathPre}/{$path}", $c);
        $this->log("music path: {$path}\n");
        $this->log("music url: {$url}\n");
        $this->log("save music to: {$this->pathPre}/{$path}");
        return $path;
    }


    //保存封面相对路径，方便迁移
    function downloadImg($mid, $level, $url){
        $typeMap = [
            'image/jpeg' => 'jpg',
            'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/apng' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
        ];
        $h = get_headers($url, 1);
        $ext = $typeMap[$h['Content-Type']];
        $subdir = substr($mid, 0, 2);
        $midGuard = sha1($mid);
        $path = "cover/{$subdir}/{$midGuard}-{$level}.{$ext}";
        @mkdir("{$this->pathPre}/cover/{$subdir}");
        $c = file_get_contents($url);
        file_put_contents("{$this->pathPre}/{$path}", $c);
        $this->log("image path: {$path}\n");
        $this->log("image url: {$url}\n");
        $this->log("save image to: {$this->pathPre}/{$path}");
        return $path;
    }


    function table($table, $force=false){
        static $objs = [];
        if (isset($objs[$table]) && $force === false) {
            return $objs[$table];
        }

        if ($GLOBALS['driver'] == 'sqlite') {
            $m = new SQLiteModel("{$GLOBALS['sqlite']['dbpath']}/{$table}.sqlite", $table);
        } else {
            $m = new Model($table, $GLOBALS['driver']);
        }

        $objs[$table] = $m;
        return $m;
    }

    function log($str){
        $now = date('Y-m-d H:i:s.').preg_replace('/\d+\./', '', microtime(1));
        $str = "[{$now}] {$str}\n";
        if ($this->debug) echo $str;
        file_put_contents("{$this->pathPre}/log.log", $str, FILE_APPEND);
    }

}


$l = new LuDouyin;
$l->debug = $GLOBALS['debug'] ?: 0;
@$mc_id = $_SERVER['argv'][1] ?: 0;
$l->doFuckTypes($mc_id);


