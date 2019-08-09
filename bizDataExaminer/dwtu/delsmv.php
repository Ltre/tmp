<?php 

/**

	private function delPics($pids){
		$pics = Yii::app()->db->createCommand('SELECT pic_id,file_id FROM piz_pic WHERE  pic_id in ('.join(',',$pids).')')->queryAll();
		Yii::import("application.extensions.*");
		$dfsObj = new MultiStorage();
		foreach($pics as $v) {
			$pictures = unserialize($v['file_id']);
			foreach ($pictures as $picture) $dfsObj->deleteFile($picture['file_id']);
		}

		$gallerys = Yii::app()->db->createCommand('SELECT gallery_id,pic_id FROM piz_gallerypics WHERE  pic_id in ('.join(',',$pids).')')->queryAll();
		foreach($gallerys as $v) if($v['pic_id']) $gids[$v['gallery_id']][] = $v['pic_id'];
		if($gallerys){
			foreach($gids as $gid=>$v){
				$count = count($v);
				if($count) Yii::app()->db->createCommand("UPDATE piz_gallery SET picsum = picsum - {$count} WHERE gallery_id = :gid")->bindParam(":gid", $gid)->execute();
			}
		}
		// 然后删除标签关联，图片关联
		Yii::app()->db->createCommand('DELETE FROM piz_gallerypics WHERE  pic_id in ('.join(',',$pids).')')->execute();

		Yii::app()->db->createCommand('DELETE FROM piz_pic WHERE  pic_id in ('.join(',',$pids).')')->execute();
	}

 */

define('BASE_DIR',dirname(__FILE__).'/');
require(BASE_DIR . 'hehe/config.php');
require(BASE_DIR . 'hehe/lib.php');
require(BASE_DIR . 'hehe/dwHttp.php');
require(BASE_DIR . 'hehe/Model.php');
require(BASE_DIR . 'hehe/SeniorModel.php');

@$batch = $_SERVER['argv'][1];
$step = 1000;

switch ($batch) {//start含end不含，区间符表示： [start, end)
    case '1':
        $start = 1;
        $end = 6504889;
        break;
    case '2':
        $start = 6504889;
        $end = 30580555;
        break;
    case '3':
        $start = 30580555;
        $end = 40929435;
        break;
    case '4':
        $start = 40929435; 
        $end = 45624965;
        break;
    case '5':
        $start = 45624965;
        $end = 52877074;
        break;
    default:
        die('fk!');
}



$pids = [];
@$M = new Model('piz_pic');
$sql = 'SELECT pic_id,file_id FROM piz_pic WHERE  pic_id in ('.join(',',$pids).')';
$pics = $M->query($sql);
foreach($pics as $v) {
    $pictures = unserialize($v['file_id']);
    
}














@$MB = new Model('club_common_member_bak');
@$M = new Model('club_common_member');

for ($curr = $start; $curr < $end;) {
    echo "from {$curr} in [{$start} ~ {$end}]; \n";//debug
    $sql = "SELECT * FROM club_common_member_bak WHERE uid >= {$curr} and uid < {$end} order by uid ASC limit {$step}";
    $list = $MB->query($sql);
    $last = null;
    foreach ($list as $k => $v) {
        $last = $v;
        echo "k: {$k} \n";
        $v['username'] = $v['uid'].'_'.buildRandStr(5);
        try {
            // var_dump($v);//debug
            $M->insert($v);
        }catch(Exception $e) {
            print_r(compact('v', 'e'));
        }
    }
    if ($last) {
        $sql = "SELECT * FROM club_common_member_bak WHERE uid > {$last['uid']} order by uid asc limit 1";
        $next = $MB->query($sql);
        if ($next) {
            $curr = $next[0]['uid'];
        } else {
            $curr ++;
        }
    }
}
