<?php
@session_start();

$config = array(
	'product_domain' => 'pagegame-admin.duowan.com',//正式使用的域名
	'rewrite' => array(
		// 'login' => 'default/login',
		// 'logout' => 'default/logout',
		'login.do' => 'default/login',
		'<c>/<a>' => '<c>/<a>',
	),
	'app_id' => 'page_game',
	'image_service' => array(
        'url' => 'http://adimageservice.dwstatic.com/upload.do',//开发广告自助系统时上线的传图接口(@date 20170811开始支持传其它文件)
    ),
    'cache_control' => array( //配置Cache.php所需的值，值的具体初始化值根据URL的cache参数决定，详见BaseController::setCacheAble()
        'CACHE_GET_ABLE' => true, //缓存是否可获取(默认：是)
        'CACHE_SET_ABLE' => true, //缓存是否可设置(默认：是)
    ),
);

$setting = array(
	"pagegame-admin.webdev2.duowan.com" => array(
		'debug' => 1,
		'mysql' => array(
			'MYSQL_HOST' => '',
			'MYSQL_PORT' => '',
			'MYSQL_USER' => '',
			'MYSQL_DB'   => '',
			'MYSQL_PASS' => '',
			'MYSQL_CHARSET' => 'utf8',
		),
		'redis'=>array(
			'host'=>'',
			'port'=>'',
            'auth' => '',
		),
	),
	"pagegame-admin.duowan.com" => array(
		'debug' => 0,
		'mysql' => array(
			'MYSQL_HOST' => '',
			'MYSQL_PORT' => '',
			'MYSQL_USER' => '',
			'MYSQL_DB'   => '',
			'MYSQL_PASS' => '',
			'MYSQL_CHARSET' => 'utf8',
		),
		'redis'=>array(
			'host'=>'',
			'port'=>'',
            'auth' => '',
		),
	),
);

define('DEBUG', $setting[$_SERVER["HTTP_HOST"]]["debug"]);
if (DEBUG) {
	define('DWAE_MMC_HOST_1', '61.160.36.225');
	define('DWAE_MMC_PORT_1', '11212');
} else {
	define('DWAE_MMC_HOST_1', '10.20.160.28');//2G
	define('DWAE_MMC_PORT_1', '11229');
	define('DWAE_MMC_HOST_2', '10.20.160.19');//2G
	define('DWAE_MMC_PORT_2', '11229');
}
$bizConfig = require(BASE_DIR.'protected/biz_config.php');//分离具体业务的配置
return $bizConfig + ($setting[$_SERVER["HTTP_HOST"]] ?: array()) + $config;