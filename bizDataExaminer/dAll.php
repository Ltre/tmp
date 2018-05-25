<?php

include 'config.php';
include 'lib.php';
include 'dwHttp.php';

$source = 'tu';
$ids = [];
if (empty($source) || empty($id)) die('wtf');
$nimabi = ltredc($GLOBALS['sources'][$source]['d']);
foreach ($ids as $id) {
    $ret = obj('dwHttp')->post($nimabi, ['id' => $id, 'sign' => md5($source.$id)]);
    echo $ret;
}
