#!/usr/local/php/bin/php
<?php
/**
 * 根据o2y，修正openid_to_yyuid_init_*.yyuid
 */

include __DIR__.'/config.php';
include __DIR__.'/Model.php';

@$t = $_SERVER['argv'][1] ?: 0;

$sql = "select openid,count(1) c from openid_to_yyuid_{$t} where openid in(select distinct(openid) from openid_to_yyuid_{$t} where status=-2) and yyuid >= pow(2, 39) group by openid having c>1 order by c";
$model = new Model('openid_to_yyuid_init', 'mysql');
$list = $model->query($sql);
$map = [];
echo "#step1:\n";
print_r($list);
echo "\n";
foreach ($list as $v) {
    $o2yList = $model->query("select * from openid_to_yyuid_{$t} where openid = :openid", ['openid' => $v['openid']]) ?: [];
    echo "#step2:\n";
    print_r($o2yList);
    echo "\n";
    foreach ($o2yList as $o2y) {
        if ($o2y['status'] == 1) {
            $initModel = new Model("openid_to_yyuid_init_{$t}", 'mysql');
            $initData = $initModel->find(['openid' => $v['openid']]);
            echo "#step3:\n";
            print_r($initData);
            echo "\n";
            if ($initData['yyuid'] != $o2y['yyuid']) {
                echo "#step4:\n";
                $success = false !== $updateRs = $initModel->update([
                    'openid' => $initData['openid'],
                    'type' => $initData['type'],
                    'yyuid' => $initData['yyuid'],
                    'appid' => $initData['appid'],
                ], ['yyuid' => $o2y['yyuid']]);
                print_r(compact('success', 'o2y', 'initData'));
            }
        }
    }
}


