<?php

function ltredc($str){
    $table = str_split('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ@~!*()-_.\'');
    $rawList = array();
    $offsetList = array();
    foreach (array_reverse(str_split($str)) as $k => $v) {
        $pos = array_search($v, $table);
        if (intval(($k + 1) % 2) == 1) {
            $offsetList[] = $pos;
        } else {
            $rawPos = intval($pos - $offsetList[($k + 1) / 2 - 1]);
            @$rawList[] = $table[$rawPos];
        }
    }
    $raw = str_replace('@', '%', implode('', $rawList));
    return urldecode($raw);
}