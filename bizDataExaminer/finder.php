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
    echo json_encode($list);
} else {
    foreach ($list as $v) {
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
    echo json_encode($findList);
}