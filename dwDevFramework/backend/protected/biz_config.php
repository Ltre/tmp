<?php
/**
 * 与业务有关的总配置
 */
$bizConfig = array(
    //管理员
    'adminList' => [
        'dw_xxx',
        'dw_yyy',
    ],
    //发票相关
    'invoice' => array(
        //发票状态
        'status' => array(
            'APPLY' => '0',
            'AGREE' => '1',
            'REJECT' => '2',
            'EXPRESS' => '3',
        ),
    ),
);


/**
 * 测试环境配置
 */
$bizConfigSpecial["mg-admin.webdev2.duowan.com"] = array(
    'api' => [
        'udbproxy' => [
            'getInfo' => [ //获取登录信息
                'url' => 'https://abc.duowan.com/api/getInfo',
                'appid' => 1888,
                'secret' => 'bX699FDCfdsafasfsadfc95fe51e0c',
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
                'url' => 'https://abc.duowan.com/api/getInfo',
                'appid' => 1888,
                'secret' => 'bX699FDCfdsafasfsadfc95fe51e0c',
            ],
        ],
    ],
);

return ($bizConfigSpecial[$_SERVER["HTTP_HOST"]] ?: array()) + $bizConfig;