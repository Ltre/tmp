#!/usr/local/php/bin/php
<?php

include __DIR__.'/config.php';
include __DIR__.'/Model.php';
include __DIR__.'/SeniorModel.php';


for ($n = 8; $n < 9; $n++) {    
    
    $tableName = "openid_to_yyuid_{$n}";
    $model = new SeniorModel($tableName, 'mysql');
    $sql = "select openid, count(*) as c from {$tableName} where status=1 and type <> 'mp' group by openid having c>1";
    $list = $model->query($sql);
    $map = [];
    $c1 = $c2 = 0;
    foreach ($list as $k => $v) {
        $checkList = $model->select(['openid' => $v['openid']]);
        $map[$k]['yes'] = $yes = yes($checkList);
        $map[$k]['no'] = $no = no($checkList, $model);
        $map[$k]['min'] = $min = mins($checkList);
        echo "YES, NO, MIN : \n";
        var_dump($yes, $no, $min);
        echo "\n";
        echo "yes == no : \n";
        var_dump($yes == $no);
        echo "\n";
        echo "yes != min : \n";
        var_dump($yes != $min);
        echo "\n";
        echo "CHECK LIST IS : \n";
        print_r($checkList);
        $map[$k]['yes == no?'] = $yes == $no ? ('yes-no=1'.print_r($checkList)) : 'yes-no=0';
        $map[$k]['yes == min?'] = $yes == $min ? 'yes-min=1' : ('yes-min=0'.print_r($checkList));
        if ($yes == $no) {
            $c1 ++;
        }
        if ($yes != $min) {
            $c2 ++;
        }
    }
    echo '####'.$tableName."\n";
    var_dump($c1, $c2);
    echo "<<<<<<< \n";
}



function yes($checkList){
    foreach ($checkList as $kk => $vv) {
        if ($vv['status'] == 1 && $vv['appid'] != 1002) {
            return $vv['yyuid'];
        }
    }
}

function no($checkList, $model){
    foreach ($checkList as $kk => $vv) {
        if ($vv['status'] == 1 && $vv['appid'] == 1002) {
            $conds = [
                'openid' => $vv['openid'],
                'type' => $vv['type'],
                'yyuid' => $vv['yyuid'],
                'status' => $vv['status'],
                'appid' => $vv['appid'],
                'outer_id' => $vv['outer_id'],
            ];
            $updateList = $model->select($conds);
            if (count($updateList) > 1) {
                echo "==============: \n";
                print_r($updateList);
                echo "@@@@@@@@@@@@@ \n";
            }
            //echo "UPDATE RESULT: ". $model->update($conds, ['status' => -2]) ."\n";            
            
            return $vv['yyuid'];
        }
    }
}

function mins($checkList) {
    $yyuids = [];
    foreach ($checkList as $kk => $vv) {
        if ($vv['status'] == 1) {
            $yyuids[] = $vv['yyuid'];
        }
    }
    sort($yyuids);
    return $yyuids[0];
}