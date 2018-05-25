<?php

include 'config.php';
include 'lib.php';
include 'dwHttp.php';

$source = $_REQUEST['source'];
$id = $_REQUEST['id'];
if (empty($source) || empty($id)) die('wtf');
$ret = obj('dwHttp')->post(ltredc($GLOBALS['sources'][$source]['d']), ['id' => $id, 'sign' => md5($source.$id)]);
echo $ret;