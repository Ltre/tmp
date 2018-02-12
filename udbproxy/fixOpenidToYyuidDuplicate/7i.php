<?php
@mkdir('7itmp', 0777, true);

//fsockopen()、fputs()、popen()
//popen("start php", 'r');
//die;
//popen();

//根据access token修补qq openid,unionid

for ($i = 1; $i <= 10000; ++$i) {
    $url = "http://udbproxy.duowan.com/?r=test/FixThirdTokenInfo_v2&p={$i}&limit=50";
    $output = file_get_contents($url);
    if ($output == 'NULL') break;
    echo $output;
    file_put_contents("7itmp/{$i}.output", $output);
    echo "$i, ";
}

for ($i = 1; $i <= 5000; ++$i) {
    $oF = "7itmp/{$i}.output";
    if (! file_exists($oF)) {
        break;
    }
    $c = file_get_contents($oF);
    file_put_contents('7itmp/merge', $c, FILE_APPEND);
    echo "$i, ";
}

die('END!!');


