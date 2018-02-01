<?php

define('STORAGE_TEST', false);

$GLOBALS = [
    'mysql' => [
        'MYSQL_HOST' => '10.25.69.228',
        'MYSQL_PORT' => '6301',
        'MYSQL_USER' => 'ff3435264962',
        'MYSQL_DB' => 'dw_feddd5a5ec3f',
        'MYSQL_PASS' => '5656581104dc94d9',
        'MYSQL_CHARSET' => 'utf8',
    ],
    'storage' => array(
        '_clusters' => array('http://imageservice.dwstatic.com/api_storage.php'),
        '_try_connect_max' => 5, // 链接重试次数
        '_tmp_dir' => '/tmp', // 临时目录
        '_group_name' => '5253bot',// 默认组名
        '_remote_url'  => 'http://s1.dwstatic.com',// 远程配置
    ),
];

