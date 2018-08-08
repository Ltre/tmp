<?php
# Step1: mysql -h10.25.69.65 -P6305 -uudbproxy_rw -p8yIxqoQwz udbproxy -e "select concat(yyuid, ',', tel) from stat_user_tel" > tmp1.txt
# Step2: /usr/local/php/bin/php exec.php > tmp1-tel.csv

function decodeTel($encodeTel){
    $tel = "";
    $telKey = "olzwugptsq";
    $keyArr = array();
    for($i=strlen($telKey)-1; $i>=0; $i--){
        $keyArr[ $telKey{$i} ] = $i;
    }
    for($i=strlen($encodeTel)-1; $i>0; $i--){
        $char = $encodeTel{$i};
        if(isset($keyArr[$char])){
            $tel = $tel.$keyArr[$char];
        }
    }
    return $tel;
}



$fp = fopen("tmp1.txt", "r");
if (! $fp) die('fp err');
$isfirstline = true;
while (!feof($fp)) {
    $buffer = fgets($fp);
    if ($isfirstline) {
        $isfirstline = false;
    } else {
        list($yyuid, $tel) = explode(',', trim($buffer));
        echo $yyuid.','.decodeTel($tel)."\n";
    }
}
fclose($fp);