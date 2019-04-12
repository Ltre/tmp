<?php
/**
 * 与业务有关的总配置
 */
$bizConfig = [
    //管理员 => ['superAdmin'=>是否有超管权限, 'spList'=>[除了role_authority.regular指定权限外，可操作的特殊权限列表]]
    'adminList' => [
        'super1'        => ['superAdmin' => true,  'spList' => [/*超管不需要指定特殊权限列表*/]],
        'super2'        => ['superAdmin' => true,  'spList' => [/*超管不需要指定特殊权限列表*/]],
        'super3'        => ['superAdmin' => true,  'spList' => [/*超管不需要指定特殊权限列表*/]],
        'dw_regular'    => ['superAdmin' => false, 'spList' => ['something1']],
    ],
    //管理的角色对应的可写操作范围
    //除了管理员以外，其他登录的用户能查看除了role_authority.superAdmin和role_authority.regular以外的内容（可以认为是查询员，比管理员低级、比游客高级的用户）
    'role_authority' => [
        'superAdmin' => [//该角色可操作superAdmin和regular的范围
            'activity/add',
            'activity/edit',
            'tool/importanddealbonuslist',
            'encour/savegoods',
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
        ],
    ],
];


/**
 * 测试环境配置
 */
$bizConfigSpecial["pagegame-admin.webdev2.duowan.com"] = array(
    'api' => [
        'udbproxy' => [
            'getInfo' => [ //获取登录信息
                'url' => 'https://udbproxy.duowan.com/api/getInfo',
                'appid' => 1009,
                'secret' => 'Yhtrv8ac95fe5aUBx8sdAAhmwSJMJQB9',
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
$bizConfigSpecial["pagegame-admin.duowan.com"] = array(
    'api' => [
        'udbproxy' => [
            'getInfo' => [ //获取登录信息
                'url' => 'https://udbproxy.duowan.com/api/getInfo',
                'appid' => 1009,
                'secret' => 'Yhtrv8ac95fe5aUBx8sdAAhmwSJMJQB9',
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