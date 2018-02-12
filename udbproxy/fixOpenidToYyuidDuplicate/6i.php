<?php
@mkdir('6itmp', 0777, true);

//fsockopen()、fputs()、popen()
//popen("start php", 'r');
//die;
//popen();

//根据access token修补qq openid,unionid

for ($i = 1; $i <= 10000; ++$i) {
    $url = "http://udbproxy.duowan.com/?r=test/FixThirdTokenInfo&p={$i}&limit=25";
    $output = file_get_contents($url);
    if ($output == 'NULL') break;
    echo $output;
    file_put_contents("6itmp/{$i}.output", $output);
    echo "$i, ";
}

for ($i = 1; $i <= 1070; ++$i) {
    $c = file_get_contents("6itmp/{$i}.output");
    file_put_contents('6itmp/merge', $c, FILE_APPEND);
    echo "$i, ";
}

die('END!!');


