<?php

include 'config.php';
include 'dwHttp.php';

$kw = $_REQUEST['kw'];
$getter = $_REQUEST['getter'];
$params = $_REQUEST['params'] ?: [];
$http = new dwHttp;
$ret = $http->post($getter, $params, 55);
$list = json_decode($ret?:'[]', 1);
foreach ($list as $v) {
    
}