<?php

include 'config.php';
include 'lib.php';
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

    function listByType($type_id, $p = 1, $limit = 100){
        $list = $this->table('mc_relate')->select(['type_id' => $type_id], '*', '', [$p, $limit, 10]) ?: [];
        $rsList = [];
        foreach ($list as $v) {
            $tmp = $this->table('mc_info')->find(['mid' => $v['mid']]);
            $tmp['cover_thumb'] = "//mcstatic.duowan.com/{$tmp['cover_thumb']}";
            $tmp['cover_medium'] = "//mcstatic.duowan.com/{$tmp['cover_medium']}";
            $tmp['cover_large'] = "//mcstatic.duowan.com/{$tmp['cover_large']}";
            $tmp['cover_hd'] = "//mcstatic.duowan.com/{$tmp['cover_hd']}";
            $tmp['save_path'] = "//mcstatic.duowan.com/{$tmp['save_path']}";
            $tmp['duration'] = (int) $tmp['duration'];
            $tmp['created'] = (int) $tmp['created'];
            $rsList[] = $tmp;
        }
        return [
            'list' => $rsList,
            'page' => $this->table('mc_relate')->page,
        ];
    }

    function types(){
        $types = $this->table('mc_type')->select();
        foreach ($types as $k => $type) {
            $types[$k]['type_cover'] = "//mcstatic.duowan.com/{$type['type_cover']}";
        }
        return $types;
    }

}

$i = new Interfacez;

switch (arg('a')) {
    case 'types':
        $types = $i->types();
        // file_put_contents("output-types.json", json_encode($types));
        exit(json_encode($types));
    case 'songs':
        $t = arg('t');
        empty($t) && exit('[]');
        $songs = $i->listByType($t, arg('p', 1), arg('limit', 100));
        // file_put_contents("output-songs-{$type['type_id']}.json", json_encode($songs));
        exit(json_encode($songs));
    default:
        die('wtf');
}
