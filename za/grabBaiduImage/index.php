<?php

include 'dwHttp.php';
include 'dwPinyin.php';

$config = [
    'word' => '斗图装逼',
    'useFilter' => 'ALL',
    'get_maybe_gif' => true,//遇到gif类型图，尽可能获取真实gif（百度网页默认不直接加载，需要鼠标hover动作）
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

$tmpdir = "tmp/" . (new dwPinyin)->str2py($config['word']) . '_' . $config['useFilter'] . '_' . date('YmdHis');
@mkdir($tmpdir, 0777, true);
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
                $imgUrl = $v['middleURL'];
                saveOne($imgUrl, function($ext) use ($tmpdir, $p, $k) {
                    return "{$tmpdir}/{$p}-{$k}.{$ext}";
                });
                if ($v['is_gif'] == 1 && $config['get_maybe_gif']) { //遇到可能是gif的图，则保存hover图到独立的目录，这里的图多数是真正的gif
                    $imgUrl = $v['replaceUrl'][1]['ObjURL'];
                    saveOne($imgUrl, function($ext) use ($tmpdir, $p, $k) {
                        @mkdir("{$tmpdir}/maybe-gif", 0777, true);
                        return "{$tmpdir}/maybe-gif/{$p}-{$k}.{$ext}";
                    });
                }
                /* $getIgs = getimagesize($imgUrl);
                if (false === $getIgs) continue;
                list ($width, $height, $mimetype) = $getIgs;
                $ext = image_type_to_extension($mimetype, false);
                $grabFile = "{$tmpdir}/{$p}-{$k}.{$ext}";
                file_put_contents("{$grabFile}", file_get_contents($imgUrl));
                echo "write file: {$grabFile} \n\n"; */
            }
        }
    }
    ++ $p;
    echo "----------------emptyCount: {$emptyCount}----------------\r\n";
    echo "----------------p: {$p}----------------\r\n";
}


function saveOne($imgUrl, Closure $genFilePath){
    $getIgs = getimagesize($imgUrl);
    if (false === $getIgs) return;
    list ($width, $height, $mimetype) = $getIgs;
    $ext = image_type_to_extension($mimetype, false);
    //$grabFile = "{$tmpdir}/{$p}-{$k}.{$ext}";
    $grabFile = $genFilePath($ext);
    file_put_contents("{$grabFile}", file_get_contents($imgUrl));
    echo "write file: {$grabFile} \n\n";
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

