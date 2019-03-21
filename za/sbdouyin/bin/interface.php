<?php

include 'config.php';
include 'Model.php';
include 'SQLiteModel.php';

/**
 * 输出数据到JSON文件（待测试）
 */
class Interfacez {

    function table($table, $force=false){
        static $objs = [];
        if (isset($objs[$table]) && $force === false) {
            return $objs[$table];
        }

        if ($GLOBALS['driver'] == 'sqlite') {
            $m = new SQLiteModel("{$GLOBALS['sqlite']['dbpath']}/{$table}.sqlite", $table);
        } else {
            $m = new Model($table, $GLOBALS['driver']);
        }

        $objs[$table] = $m;
        return $m;
    }

    function listByType($mc_id){
        $list = $this->table('mc_relate')->select(['mc_id' => $mc_id], '*', '', [$p, $limit, 10]) ?: [];
        $rsList = [];
        foreach ($list as $v) {
            $rsList[] = $this->table('mc_info')->find(['mid' => $v['mid']]);
        }
        return [
            'list' => $rsList,
            'page' => $this->table('mc_relate')->page,
        ];
    }

    function types(){
        return $this->table('mc_type')->select();
    }

}

$i = new Interfacez;
$types = $i->types();
var_dump($types);
file_put_contents("output-types.json", json_encode($types));
foreach ($types as $type) {
    $songs = $i->listByType($type['mc_id']);
    file_put_contents("output-songs-{$type['mc_id']}.json", json_encode($songs));
}