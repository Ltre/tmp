<?php

define('DEBUG', 1);

$GLOBALS = [
    'mysql' => [
			'MYSQL_HOST' => '10.21.46.86',
			'MYSQL_PORT' => '6301',
			'MYSQL_USER' => 'net_dwbbs_rw',
			'MYSQL_DB'   => 'dx2',
			'MYSQL_PASS' => '1yQzDffK+0..',
			'MYSQL_CHARSET' => 'utf8',
    ],
    'mysql_dev' => [
        'MYSQL_HOST' => '61.160.36.225',
        'MYSQL_PORT' => '3306',
        'MYSQL_USER' => 'ojiatest',
        'MYSQL_DB'   => 'dx2',
        'MYSQL_PASS' => 'ojia305',
        'MYSQL_CHARSET' => 'utf8',
    ],
];


//环境切换代码
if (DEBUG) {
    $GLOBALS['mysql'] = $GLOBALS['mysql_dev'];
}