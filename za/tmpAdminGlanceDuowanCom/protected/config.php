<?php
@session_start();

$config = array(
	'rewrite' => array(
		// '<username>/hello.html' => 'default/index',
		// 'dev/<a>.html' => 'default/<a>',
        'trailer/add.do' => 'trailer/add',
        'video/notify.do' => 'video/notify',
        'search/article.do' => 'search/article',
        'commentary/add.do' => 'commentary/add',
        'admin/getAllUps.do' => 'admin/getAllUps',
	),
	'app_id' => 'demo',
);

$setting = array(
	"admin-glance.duowan.com" => array(
		'debug' => 1,
		'mysql' => array(
			'MYSQL_HOST' => '',
			'MYSQL_PORT' => '',
			'MYSQL_USER' => '',
			'MYSQL_DB'   => '',
			'MYSQL_PASS' => '',
			'MYSQL_CHARSET' => 'utf8',
		),
	),
	"localhost:1234" => array(
		'debug' => 1,
		'mysql' => array(
			'MYSQL_HOST' => '',
			'MYSQL_PORT' => '',
			'MYSQL_USER' => '',
			'MYSQL_DB'   => '',
			'MYSQL_PASS' => '',
			'MYSQL_CHARSET' => 'utf8',
		),
	),
);
define('DEBUG', $setting[$_SERVER["HTTP_HOST"]]["debug"]);
return $setting[$_SERVER["HTTP_HOST"]] + $config;