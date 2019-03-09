<?php

include 'config.php';
include 'Model.php';

//      /usr/local/php/bin/php listbytype2.php

class FuckDouyin {

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
            "https://api.amemv.com/aweme/v1/music/list/?mc_id='.$mc_id.'&cursor=0&count=30&ts=1552123219&js_sdk_version=1.10.4&app_type=normal&manifest_version_code=530&_rticket=1552123219697&ac=wifi&device_id=48912932681&iid=65343929505&mcc_mnc=46000&os_version=8.0.0&channel=huawei&version_code=530&device_type=VTR-AL00&language=zh&resolution=1080*1920&openudid=6b1e80386b74f00c&update_version_code=5302&app_name=aweme&version_name=5.3.0&os_api=26&device_brand=HUAWEI&ssmix=a&device_platform=android&dpi=480&aid=1128&as=a115c87893752c35634388&cp=8f5ac25034398857e1OyWg&mas=01888c81175f386ffb492b1e8ac6d153c91c1ccc2ccc6cacccc64c" \
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



    function doFuck($mc_id, $cursor = 0){
        do {
            list ($succ, $msg, $resp, $list, $hasMore, $cursor) 
                = $this->getListByType($mc_id, $cursor, $hasMore);
            if (! $succ) {
                throw new Exception($msg);
            }
            $this->save($list);
        } while ($hasMore);
    }


    function save($list){
        $musicT = $this->table('mc_info');//@todo 新建一个库再跑
        foreach ($list as $v) {
            $find = $musicT->find(['music_id' => $v['mid']]);
            $data = [
                'cover_thumb' => $this->downloadImg($v['mid'], 'thumb', $v['cover_thumb']['url_list'][0]),
                'cover_medium' => $this->downloadImg($v['mid'], 'medium', $v['cover_medium']['url_list'][0]),
                'cover_large' => $this->downloadImg($v['mid'], 'large', $v['cover_large']['url_list'][0]),
                'cover_hd' => $this->downloadImg($v['mid'], 'hd', $v['cover_hd']['url_list'][0]),
                'title' => $v['title'],
                'play_url' => $v['play_url']['url_list'][0],
                'duration' => $v['duration'],
                'save_path' => $this->downloadMusic($v['mid'], $v['play_url']),
                'created' => time(),
            ];
            if ($find) {
                $musicT->update(['music_id' => $v['mid']], $data);
            } else {
                $musicT->insert($data);
            }
        }
    }


    function downloadMusic($mid, $url){
        $typeMap = [
            'mpeg/mp3' => 'mp3',
            // '' => 'aac',
            // '' => 'ogg',
            // '' => 'm4a',
        ];
        $h = get_headers($url, 1);
        $ext = $typeMap[$h['Content-Type']];
        $path = "/path/to/music/{$mid}.{$ext}";
        $c = file_get_contents($url);
        file_put_contents($path, $c);
        return $path;
    }


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
        $path = "/path/to/cover/{$mid}-{$level}.{$ext}";
        return $path;
    }


    function table($table, $force=false){
        static $objs = [];
        if (isset($objs[$table]) && $force === false) {
            return $objs[$table];
        }
        $m = new Model($table, 'mysql_dev');
        $objs[$table] = $m;
        return $m;
    }

}

(new FuckDouyin)->doFuck(84);