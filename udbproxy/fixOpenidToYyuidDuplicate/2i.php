#!/usr/local/php/bin/php
<?php

include __DIR__.'/config.php';
include __DIR__.'/Model.php';
include __DIR__.'/SeniorModel.php';

$map = [];
for ($n = 0; $n < 10; $n++) {
    $tableName = "openid_to_yyuid_{$n}";
    $model = new SeniorModel($tableName, 'mysql');
    $sql = "select openid, count(*) as c from {$tableName} where status=1 group by openid having c>1";
    $list = $model->query($sql);
    $c1 = 0;
    foreach ($list as $k => $v) {
        $checkList = $model->select(['openid' => $v['openid']]);
        $map[$k]['yes'] = $yes = yes($checkList);
        $map[$k]['no'] = $no = no($checkList, $model);        
        $map[$k]['yes != no?'] = $yes != $no ? ('yes-no=1'.print_r($checkList)) : 'yes-no=0';
        if ($yes == $no) {
            $c1 ++;
        }
        
        //统计MP数
        foreach ($checkList as $vv) {
            if ($vv['status'] != 1 || $vv['type'] != 'mp') continue;            
            @$map['count_map'][$vv['type']] ++;
            if ($vv['yyuid'] < 2147483648) {
                @$map['count_map']['< 2147483648'] ++;
            } else {
                @$map['count_map']['>= 2147483648'] ++;
            }
            /* $model->update([
                'openid' => $vv['openid'],
                'type' => $vv['type'],
                'yyuid' => $vv['yyuid'],
                'status' => $vv['status'],
                'appid' => $vv['appid'],
                'outer_id' => $vv['outer_id'],
            ], ['status' => -2]);
            echo "!!!!!!!!! FUCK THE MP TO -2: \n";
            echo json_encode([[
                'openid' => $vv['openid'],
                'type' => $vv['type'],
                'yyuid' => $vv['yyuid'],
                'status' => $vv['status'],
                'appid' => $vv['appid'],
                'outer_id' => $vv['outer_id'],
            ], ['status' => -2, 'type' => 'weixin']]);
            echo "\n"; */
        }
        
    }
    echo '####'.$tableName."\n";
    var_dump($c1);
    echo "<<<<<<< \n";
}
echo "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%\n";
print_r($map);



function yes($checkList){
    foreach ($checkList as $kk => $vv) {
        if ($vv['status'] == 1 && $vv['type'] == 'weixin') {
            return $vv['yyuid'];
        }
    }
}

function no($checkList, $model){
    foreach ($checkList as $kk => $vv) {
        if ($vv['status'] == 1 && $vv['type'] == 'mp') {
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
