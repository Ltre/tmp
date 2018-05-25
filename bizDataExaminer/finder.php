<?php

include 'config.php';
include 'dwHttp.php';

$source = $_REQUEST['source'];
@$kws = $_REQUEST['kws'] ?: [];
@$id = $_REQUEST['id'] ?: 1;
@$limit = $_REQUEST['limit'] ?: 1000;
$kwFields = $GLOBALS['sources'][$source]['keywordFields'];

$getter = $GLOBALS['sources'][$source]['list_api'];
$http = new dwHttp;
$ret = $http->post($getter, compact('id', 'limit'), 55);
$list = json_decode($ret?:'[]', 1);
$findList = [];
if (empty($kws)) {
    $kws = file('kws.txt');
    $kws = array_unique(array_filter($kws));
}
$lastId = 1;
foreach ($list as $v) {
    $lastId = $v['id'];
    if (is_array($v)) {
        foreach ($kwFields as $field) {
            if (! isset($v[$field])) continue;
            foreach ($kws as $kw) {
                if ('' !== $kw && false !== mb_strpos($v[$field], $kw)) {
                    $findList[] = $v;
                    continue 3;
                }
            }
        }
    }
}
echo json_encode(['list' => $findList, 'lastId' => $lastId]);
