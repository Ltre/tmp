<?php

$GLOBALS = [
    'debug' => 1,
    'mysql_dev' => [
        'MYSQL_HOST'=>'61.160.36.225',
        'MYSQL_PORT'=>'3306',
        'MYSQL_USER'=>'ojiatest',
        'MYSQL_PASS'=>'ojia305',
        'MYSQL_DB'=>'douyin_media',
        'MYSQL_CHARSET'=>'UTF8',
    ],
    'mysql_prod' => [
        'MYSQL_HOST'=>'10.21.43.42',
        'MYSQL_PORT'=>'6304',
        'MYSQL_USER'=>'douyin_media_rw',
        'MYSQL_PASS'=>'E54J3BZ5Qp',
        'MYSQL_DB'=>'dou_yin',
        'MYSQL_CHARSET'=>'UTF8',
    ],
    'sqlite' => [
        'dbpath' => 'sqlitedata',
    ],
    'driver' => 'mysql_prod',//sqlite, mysql_dev, mysql_prod
    'resource' => '/data1/webapps/mcstatic.duowan.com',// /tmp/pio_test
];
