<?php


echo shell_exec('curl -A "com.ss.android.ugc.aweme/530 (Linux; U; Android 8.0.0; zh_CN_#Hans; VTR-AL00; Build/HUAWEIVTR-AL00; Cronet/58.0.2991.0)" \
    -b "install_id=65343929505; ttreq=1$d7a1dd3c78fdfc6c0a392d01870aad0a90aad819; odin_tt=e08ae73c9ba20641a66a9b2fb888ea7a0672f38beecc0efe5e2f6205609428b7a5796455e568ea6c309147478477d9d7; sid_guard=0cf354f1237af5f7104349f9c0c516bc%7C1551865956%7C5184000%7CSun%2C+05-May-2019+09%3A52%3A36+GMT; uid_tt=4024e1833ba336fbf3c5c5f154356f61; sid_tt=0cf354f1237af5f7104349f9c0c516bc; sessionid=0cf354f1237af5f7104349f9c0c516bc; qh[360]=1" \
    -H "Host: api.amemv.com" \
    -H "Connection: keep-alive" \
    -H "X-SS-REQ-TICKET: 1552123398554" \
    -H "X-Tt-Token: 000cf354f1237af5f7104349f9c0c516bc935cfc3e394b34009e4ac65998088402b276f5eaa0fa2e9377e531e600bc02f44b" \
    -H "sdk-version: 1" \
    -H "X-Khronos: 1552123398" \
    -H "X-Gorgon: 03006cc00000dc9a2db3a76ab9c9eccb135c4fc5dff8f97fbab1" \
    -H "X-Pods: " \
    "https://api.amemv.com/aweme/v1/music/list/?mc_id=31&cursor=0&count=30&ts=1552123219&js_sdk_version=1.10.4&app_type=normal&manifest_version_code=530&_rticket=1552123219697&ac=wifi&device_id=48912932681&iid=65343929505&mcc_mnc=46000&os_version=8.0.0&channel=huawei&version_code=530&device_type=VTR-AL00&language=zh&resolution=1080*1920&openudid=6b1e80386b74f00c&update_version_code=5302&app_name=aweme&version_name=5.3.0&os_api=26&device_brand=HUAWEI&ssmix=a&device_platform=android&dpi=480&aid=1128&as=a115c87893752c35634388&cp=8f5ac25034398857e1OyWg&mas=01888c81175f386ffb492b1e8ac6d153c91c1ccc2ccc6cacccc64c" \
    > shabidouyin.txt');

die;


require 'lib.php';

//return [succ, msg, resp, list, hasMore]
function getListByType($mc_id, $cursor = 0, $count = 30){
    $ms = intval(microtime(true)*1000);
//debug
$ms = '1552128401627';
$now = 1552128401;
    $url = 'https://api.amemv.com/aweme/v1/music/list?';
    $url .= http_build_query([
        'mc_id' => $mc_id,
        'cursor' => $cursor,
        'count' => $count,
        'ts' => $now,
        'js_sdk_version' => '1.10.4',
        'app_type' => 'normal',
        'manifest_version_code' => '530',
        // '_rticket' => $ms,
        '_rticket' => '1552128401629',
        'ac' => 'wifi',
        'device_id' => '48912932681',
        'iid' => '65343929505',
        'mcc_mnc' => '46000',
        'os_version' => '8.0.0',
        'channel' => 'huawei',
        'version_code' => '530',
        'device_type' => 'VTR-AL00',
        'language' => 'zh',
        'resolution' => '1080*1920',
        'openudid' => '6b1e80386b74f00c',
        'update_version_code' => '5302',
        'app_name' => 'aweme',
        'version_name' => '5.3.0',
        'os_api' => 26,
        'device_brand' => 'HUAWEI',
        'ssmix' => 'a',
        'device_platform' => 'android',
        'dpi' => 480,
        'aid' => 1128,
        'as' => 'a1c559f8c119cc39534577',
        'cp' => '9690c05d143d8e9ce1MyUg',
        'mas' => '0151ca2a9c14010f393272be77cc80e1eb3c76b049197fececac2cccac9cccc6c6a728e35606426fdaef66581c1ccc2ccc6c6c26c62c',
    ]);
    $header = 'Cookie: install_id=65343929505; ttreq=1$d7a1dd3c78fdfc6c0a392d01870aad0a90aad819; odin_tt=e08ae73c9ba20641a66a9b2fb888ea7a0672f38beecc0efe5e2f6205609428b7a5796455e568ea6c309147478477d9d7; sid_guard=0cf354f1237af5f7104349f9c0c516bc%7C1551865956%7C5184000%7CSun%2C+05-May-2019+09%3A52%3A36+GMT; uid_tt=4024e1833ba336fbf3c5c5f154356f61; sid_tt=0cf354f1237af5f7104349f9c0c516bc; sessionid=0cf354f1237af5f7104349f9c0c516bc; qh[360]=1';
    $header .= "\nAccept-Encoding: gzip";
    $header .= "\nX-SS-REQ-TICKET: ".$ms;
    $header .= "\nX-Tt-Token: 000cf354f1237af5f7104349f9c0c516bc935cfc3e394b34009e4ac65998088402b276f5eaa0fa2e9377e531e600bc02f44b";
    $header .= "\nsdk-version: 1";
    $header .= "\nUser-Agent: com.ss.android.ugc.aweme/530 (Linux; U; Android 8.0.0; zh_CN_#Hans; VTR-AL00; Build/HUAWEIVTR-AL00; Cronet/58.0.2991.0)";
    $header .= "\nX-Khronos: ".time();
    $header .= "\nX-Gorgon: 03003e3b80000ca77d3cc21f281e4970aed04f7a2ea592c7eb1d";
    $header .= "\nX-Pods: ";
// die($url);
    $resp = curlGet($url, $header, true);
    var_dump($resp);die;
    if (false === $resp) {
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


list ($succ, $msg, $resp, $list, $hasMore, $cursor) = getListByType(84);
var_dump($succ, $msg, $resp, $list, $hasMore, $cursor);