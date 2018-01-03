<?php

include 'dwHttp.php';
include 'dwPinyin.php';

$config = [
    'word' => '杨幂',
    //'tmpdir' => 'tmp'.date('YmdHis'),
    'useFilter' => 'ALL',
    'filterParamsMap' => [
        'ALL' => [ //全部类型
            'lm' => '',
            'st' => '-1',
            's' => '',
            'face' => '',
        ],
        'AVATAR' => [ //头像图片
            'lm' => '',
            'st' => '-1',
            's' => '3',
            'face' => '',
        ],
        'FACE' => [ //面部特写
            'lm' => '',
            'st' => '-1',
            's' => '',
            'face' => '1',
        ],
        'CARTOON' => [ //卡通画
            'lm' => '',
            'st' => '1',
            's' => '',
            'face' => '',
        ],
        'SIMPLE' => [ //简笔画
            'lm' => '',
            'st' => '2',
            's' => '',
            'face' => '',
        ],
        'DYNAMIC' => [ //动态图片
            'lm' => '6',
            'st' => '-1',
            's' => '',
            'face' => '',
        ],
        'STATIC' => [ //静态图片
            'lm' => '7',
            'st' => '-1',
            's' => '',
            'face' => '',
        ],
    ],
];


$tmpdir = "tmp/" . (new dwPinyin)->str2py($config['word']) . '_' . (new dwPinyin)->str2py($config['useFilter']) . '_' . date('YmdHis');
@mkdir($tmpdir, 0777, true);
die;
$p = 1;
$emptyCount = 0;
while ($emptyCount < 3) {
    $list = getWebImageList($config['word'], $p);
    if (empty($list)) {
        ++ $emptyCount;
    } else {
        //$len = $ret['listNum'];//可能没用
        foreach ($list as $k => $v) {
            if (isset($v['middleURL'])) {
                $getIgs = getimagesize($v['middleURL']);
                if (false === $getIgs) continue;
                list ($width, $height, $mimetype) = $getIgs;
                $ext = image_type_to_extension($mimetype, false);
                $grabFile = "{$tmpdir}/{$p}-{$k}.{$ext}";
                file_put_contents("{$grabFile}", file_get_contents($v['middleURL']));
                echo "write file: {$grabFile} \n\n";
            }
        }
    }
    ++ $p;
    echo "----------------emptyCount: {$emptyCount}----------------\r\n";
    echo "----------------p: {$p}----------------\r\n";
}


function getWebImageList($word, $p){
    global $config;
    //$url = 'https://tu.baidu.com/search/acjson?tn=resultjson_com&ipn=rj&ct=201326592&is=&fp=result&queryWord=%E6%9D%A8%E5%B9%82&cl=2&lm=&ie=utf-8&oe=utf-8&adpicid=&st=-1&z=&ic=0&word=%E6%9D%A8%E5%B9%82&s=&se=&tab=&width=&height=&face=1&istype=2&qc=&nc=&fr=&gsm=5a&1514943603709=&pn=90&rn=30';
    $url = 'https://tu.baidu.com/search/acjson?';
    $rn = 30;
    $pn = ($p - 1) * $rn;
    $params = [
        'tn' => 'resultjson_com',
        'ipn' => 'rj',
        'ct' => '201326592',
        'is' => '',
        'fp' => 'result',
        'queryWord' => $word,
        'cl' => '2',
        'lm' => '',
        'ie' => 'utf-8',
        'oe' => 'utf-8',
        'adpicid' => '',
        'st' => '-1',
        'z' => '',
        'ic' => '0',
        'word' => $word,
        's' => '',
        'se' => '',
        'tab' => '',
        'width' => '',
        'height' => '',
        'face' => '',
        'istype' => '2',
        'qc' => '',
        'nc' => '',
        'fr' => '',
        'gsm' => '5a',
        '1514943603709' => '',
        'pn' => $pn,
        'rn' => $rn,
    ];
    $coverageParams = $config['filterParamsMap'][$config['useFilter']];
    $params = array_merge($params, $coverageParams); //用图片类型筛选条件覆盖部分参数
    $url .= http_build_query($params);
    $ret = (new dwHttp)->get($url);
    $ret = json_decode($ret?:'[]', 1);
    if (isset($ret['data']) && is_array($ret['data']) && $ret['listNum'] > 0) {
        return $ret['data'];
    } else {
        return [];
    }
}



function getExtByUrl($url){
    $map = [
        
    ];
    $hs = get_header($url, 1);
    $t = $hs['Content-Type'];
    return isset($map[$t]) ? ".{$map[$t]}" : '';
}