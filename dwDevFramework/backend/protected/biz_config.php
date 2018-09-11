<?php
/**
 * 与业务有关的总配置
 */
$bizConfig = [
    //管理员 => ['acList'=>[可操作活动ID列表], 'superAdmin'=>是否有超管权限]
    'adminList' => [//1~4lol春季赛事, 5球球大作战, 6~15定为2018LPL夏季赛, 16~19定为LCK夏季常规赛, 20定为2018LOL洲际赛, 21定为绝地求生PGI, 22定为球球大作战塔坦杯, 23-LCK季后赛活动(运营忽略积分), 24-2018亚运会英雄联盟赛事（运营忽略积分）, 25-开发人员测试专用, 26-2018LPL夏季赛季后赛
        'dw_fangkunbiao'     => ['superAdmin' => true,  'acList' => [/*超管不需要指定活动列表*/]],
        'qq-70k02wow'        => ['superAdmin' => true,  'acList' => [/*超管不需要指定活动列表*/]],
        'dw_zhangzhe2'       => ['superAdmin' => true, 'acList' => []],//长沙接手的后端技术：张哲
        'dw_huangruizi'      => ['superAdmin' => false, 'acList' => [1,2,3,4,    6,7,8,9,10,11,12,13,14,15, 16,17,18,19, 20, 21, 23, 24, 25, 26]],
        'dw_chenzhan'        => ['superAdmin' => false, 'acList' => [1,2,3,4,    6,7,8,9,10,11,12,13,14,15, 16,17,18,19, 20, 21, 23, 24, 25, 26]],
        'dw_suhedong'        => ['superAdmin' => false, 'acList' => [1,2,3,4,    6,7,8,9,10,11,12,13,14,15, 16,17,18,19, 20, 21, 23, 24, 25, 26]],
        'dw_yuanlihong2'     => ['superAdmin' => false, 'acList' => [1,2,3,4,    6,7,8,9,10,11,12,13,14,15, 16,17,18,19, 20, 21, 23, 24, 25, 26]],
        'dw_zhangzhihong'    => ['superAdmin' => false, 'acList' => [1,2,3,4,    6,7,8,9,10,11,12,13,14,15, 16,17,18,19, 20, 21, 23, 24, 25, 26]],
        'dw_yanghaohao'      => ['superAdmin' => false, 'acList' => []],
        'dw_zhangyi6'        => ['superAdmin' => false, 'acList' => [5, 22]],
        '1845164595yy'       => ['superAdmin' => false, 'acList' => [5, 22]],//dw_zhangyi6的小号
        'bingbinghaibushuo'  => ['superAdmin' => false, 'acList' => [5, 22]],//长沙201808负责球球大作战的新编辑
        'dw_zhuangyue'       => ['superAdmin' => false, 'acList' => [21]],
        'sa77050578'         => ['superAdmin' => false, 'acList' => [21]],//长沙201807新来编辑郑余彬
        'jzj985097797'       => ['superAdmin' => false, 'acList' => [1,2,3,4,    6,7,8,9,10,11,12,13,14,15, 16,17,18,19, 20, 23, 24, 25, 26]],//LOL组长沙新编辑
    ],
    //管理的角色对应的可写操作范围
    'role_authority' => [
        'superAdmin' => [//该角色可操作superAdmin和regular的范围
            'activity/add',
            'activity/edit',
            'tool/importanddealbonuslist',
            'encour/savegoods',//奖品配置暂时不开放普管操作，防止出错
        ],
        'regular' => [//该角色仅可操作regular的范围
            'activity/datelistformatches',
            'activity/weeklistformatches',
            'guess/save',
            'guess/setanswers',
            'guess/clearanswers',
            'guess/deloption',
            'guess/dealwinanswerbymatch',
            'matches/create',
            'matches/edit',
            'matches/del',
            'matches/dealresultonchangewinanswer',
            'matches/teams',
            'score/adjust',
            'encour/create',
            'encour/edit',
            'encour/save',
            'encour/tasklist',
            'encour/schedule',
            'encour/open',
            'encour/del',
            'encour/goodslist',
            'encour/addgoods',
            'encour/editgoods',
            // 'encour/savegoods',
        ],
    ],
    //活动相关
    'activity' => [
        //Cookie:当前选定的活动ID
        'ac_id_cookie' => 'mg-activity',
        //线上限制修改的活动ID，防止修改结束时间造成的其它数据连锁问题
        'deny' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14],
    ],
    //比赛相关
    'matches' => [
        //比赛进行状态
        'status_desc' => [
            '0' => '未开始',
            '1' => '进行中',
            '2' => '已结束',
            '3' => '已暂停',
            '4' => '已取消',
        ],
        //比赛结果
        'result_desc' => [
            '0' => '未知',
            '1' => '主队胜',
            '2' => '客队胜',
        ],
        //比赛类型
        'types' => ['BO1', 'BO3', 'BO5'],
    ],
    //竞猜相关
    'guess' => [
        //竞猜类型
        'type_desc' => [
            '0' => '缺省类型',
            '1' => '比赛总胜负型',
            '2' => '实况问题型',
        ],
        //候选项正确性
        'correctness_desc' => [
            '0' => '未知',
            '1' => '正确',
            '2' => '错误',
        ],
        //默认的胜负竞猜提前时间，以比赛开始结束时间作为参照
        'default_win_early' => [
            'start' => [86400, '前一天'], //开始答题时间初始值为：比赛开始时间的前一天
            'end' => [1200, '前20分钟'], //结束答题时间初始值为：比赛结束时间的前20分钟
        ]
    ],
    //积分相关
    'score' => [
        //胜负竞猜的默认奖励、扣除分数
        'win_default' => [
            'bonus' => 60,
            'deduct' => 0,
        ],
        //流水类型
        'detail_type_desc' => [
            '0' => '缺省类型', 
            '1' => '竞猜奖励', 
            '-1' => '竞猜扣减',
            '2' => '手动增加',
            '-2' => '手动扣减',
            '3' => '初始化分数',
            '4' => '签到奖励',
            '5' => '助力分享',
        ],
        //积分明细倍数
        'detail_times' => [1, 2, 3],
        //活动ID => 积分明细倍数按竞猜日期规则，没指定的默认为1
        'detail_times_for_date' => [
            '1' => [
                '20180428' => 3, //活动ID=1, 当天三倍奖励
            ],
            '2' => [],
            '3' => [],
            '4' => [],
            '5' => [],
            '6' => [],
            '7' => [],
            '8' => [],
            '9' => [],
            '10' => [],
            '11' => [],
            '12' => [],
            '13' => [],
            '14' => [],
            '15' => [],
            '16' => [],
            '17' => [],
            '18' => [],
            '19' => [],
            '20' => [],
            '21' => [],
            '22' => [],
            '23' => [],//不竞猜、运营人员忽略积分
            '24' => [],//不竞猜、运营人员忽略积分
            '25' => [],//开发人员测试专用
            '26' => [
                '20180914' => 3,
            ],
        ],
        //活动ID => 排行榜上榜个数
        'rankNum4Activity' => [
            '1' => 50, //活动ID=1的，设置为前50名上榜
            '2' => 9, //前9名上榜
            '3' => 16, //前16名上榜
            '4' => 8, //前8名上榜
            '5' => 15, //前8名上榜
            '6' => 11, //前11名上榜
            '7' => 11,
            '8' => 12,
            '9' => 8,
            '10' => 14,
            '11' => 10,
            '12' => 7,
            '13' => 6,
            '14' => 5,
            '15' => 6,
            '16' => 8,
            '17' => 6,
            '18' => 6,
            '19' => 8,
            '20' => 16,
            '21' => 30,
            '22' => 5,
            '23' => 1,//不竞猜、运营人员忽略积分
            '24' => 1,//不竞猜、运营人员忽略积分
            '25' => 1,//开发人员测试专用
            '26' => 28,
        ],
    ],
];


/**
 * 测试环境配置
 */
$bizConfigSpecial["mg-admin.webdev2.duowan.com"] = array(
    'api' => [
        'udbproxy' => [
            'getInfo' => [ //获取登录信息
                'url' => 'https://udbproxy.duowan.com/api/getInfo',
                'appid' => 1006,
                'secret' => 'bX699FDCC39Ee919286F8ac95fe51e0c',
            ],
            'getUidByUDB' => [ //201712新UDB登录上线后，UDB转换YYUID
                'url' => 'http://udbproxy.duowan.com/api/getYyuidByUdb',
            ],
            'getUdbByUid' => [ //201712新UDB登录上线后，YYUID转换UDB
                'url' => 'http://udbproxy.duowan.com/api/getUdbByYyuid',
            ],
        ],
    ],
);

$bizConfigSpecial["backend_hex_match.webdev3.ouj.com"] = array(
    'api' => [
        'udbproxy' => [
            'getInfo' => [ //获取登录信息
                'url' => 'https://udbproxy.duowan.com/api/getInfo',
                'appid' => 1006,
                'secret' => 'bX699FDCC39Ee919286F8ac95fe51e0c',
            ],
            'getUidByUDB' => [ //201712新UDB登录上线后，UDB转换YYUID
                'url' => 'http://udbproxy.duowan.com/api/getYyuidByUdb',
            ],
            'getUdbByUid' => [ //201712新UDB登录上线后，YYUID转换UDB
                'url' => 'http://udbproxy.duowan.com/api/getUdbByYyuid',
            ],
        ],
    ],
);

$bizConfigSpecial["backend_hex_match.webdev3.duowan.com"] = array(
    'api' => [
        'udbproxy' => [
            'getInfo' => [ //获取登录信息
                'url' => 'https://udbproxy.duowan.com/api/getInfo',
                'appid' => 1006,
                'secret' => 'bX699FDCC39Ee919286F8ac95fe51e0c',
            ],
            'getUidByUDB' => [ //201712新UDB登录上线后，UDB转换YYUID
                'url' => 'http://udbproxy.duowan.com/api/getYyuidByUdb',
            ],
            'getUdbByUid' => [ //201712新UDB登录上线后，YYUID转换UDB
                'url' => 'http://udbproxy.duowan.com/api/getUdbByYyuid',
            ],
        ],
    ],
);

/**
 * 正式环境配置
 */
$bizConfigSpecial["mg-admin.duowan.com"] = array(
    'api' => [
        'udbproxy' => [
            'getInfo' => [ //获取登录信息
                'url' => 'https://udbproxy.duowan.com/api/getInfo',
                'appid' => 1006,
                'secret' => 'bX699FDCC39Ee919286F8ac95fe51e0c',
            ],
            'getUidByUDB' => [ //201712新UDB登录上线后，UDB转换YYUID
                'url' => 'http://udbproxy.duowan.com/api/getYyuidByUdb',
            ],
            'getUdbByUid' => [ //201712新UDB登录上线后，YYUID转换UDB
                'url' => 'http://udbproxy.duowan.com/api/getUdbByYyuid',
            ],
        ],
    ],
);

return ($bizConfigSpecial[$_SERVER["HTTP_HOST"]] ?: array()) + $bizConfig;