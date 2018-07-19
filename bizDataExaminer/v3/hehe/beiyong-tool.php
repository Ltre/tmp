<?php
//词语去重复工具
$list = file('kws.txt');
$list = array_unique($list);
foreach ($list as $l) {
    file_put_contents('kws.uniq.txt', $l, FILE_APPEND);
}