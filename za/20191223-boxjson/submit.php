<?php

//name -> name
//#id_content content -> msg_raw
//#id_priority priority -> priority
// ??? -> msg   -- http://tool.duowan.com/postman/content/popup_content.777.html
//conditions . 
    //#id_popup_if_less_than_this popup_if_less_than_this -> popup_if_less_than_this  --1000000
    

$r = $_REQUEST;


//字段show_times
if ($r['show_time']) $showTimes = explode('~', $r['show_time']);
if (isset($showTimes) && count($showTimes) == 2) {
    $showTimes[0] = trim($showTimes[0]);
    $showTimes[1] = trim($showTimes[1]);
} else {
    $showTimes = [
        date('Y-m-d').'T00:00:01',
        date('Y-m-d').'T23:59:59',
    ];
}



//字段 ID
$id = file_get_contents("id.txt");
if ($id && is_numeric($id)) {
    $id = (int) $id;
    if ($id < 1) {
        $id = 1;
    }
} else {
    $id = 1;
}
file_put_contents("id.txt", ++ $id);



$data = [
    'name' => $r['name'],
    'msg_raw' => $r['content'],
    'priority' => $r['priority'],
    'msg' => '', // ??? 例：http://tool.duowan.com/postman/content/popup_content.777.html
    'conditions' => [
        'popup_if_less_than_this' => $r['popup_if_less_than_this'],
        'triggers' => [//写死：交互触发条件，只能选类型 b '关闭游戏大厅后弹'
            'count' => null,
            'program_name' => '',
            'name' => 'd',
        ],
        'processes_not_exist' => [],//写死：忽略Processes not exist的内容
        'window_height' => $r['window_height'],
        'window_width' => $r['window_width'],
        'extra_args' => $r['extra_args'],
        'os_version' => [//写死，应该基本不改
            "all",
            "5.1",
            "5.2",
            "6.0",
            "6.1",
            "6.2",
            "6.3",
            "10.0",
        ],
        'window_pos' => trim($r['window_pos']),
        'processes_exist' => [//写死：以后考虑用 | 分割成数组
            "WOTBox.exe",
        ],
        'show_times' => [//这里结构没错，就是  [[]] 型
            $showTimes
        ],
        'region_ids' => [//写死
            "350000",
            "650000",
            "640000",
            "440000",
            "500000",
            "620000",
            "230000",
            "410000",
            "340000",
            "320000",
            "110000",
            "430000",
            "330000",
            "450000",
            "520000",
            "610000",
            "140000",
            "630000",
            "540000",
            "460000",
            "120000",
            "210000",
            "360000",
            "150000",
            "220000",
            "130000",
            "510000",
            "530000",
            "370000",
            "310000",
            "420000",
        ],
    ],
    'id' => $id,
];

@mkdir("json", 0777, true);
file_put_contents("json/{$id}.json", json_encode([$data]));

echo json_encode([$data]);