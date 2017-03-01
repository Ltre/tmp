<?php
date_default_timezone_set('PRC');
ini_set("display_errors", "On");
error_reporting(E_ALL & ~E_STRICT);
/**
 * 声明：本脚本仅适用于四月开始的计算
 */
mb_internal_encoding("GB2312");//本文件运行编码环境为简体中文

define('START_TIME', 20170201);
define('END_TIME', 20170228);
define('GET_VIDS_URL', "http://v.huya.com/?r=test/GetVidByUid&calcMore=1&uid=");//获取vid和duration（多获取分月的数据）
define('GET_VV_URL', "http://playstats-manager.v.duowan.com/?r=api/getAdPlay&startTime=".START_TIME."&endTime=".END_TIME."&vid="); 

$uids = file("uids.txt");
$obj = new fengcheng();
$obj->calc($uids);

class fengcheng{
    
    protected $uidRatioMap = array();//[uid] => array('web'=>0.003, 'wap'=>0.0015, 'app'=>0.0015)
    
    protected $uidVVDefault = array(
        'uid'=>0, 
        //'全端全播'=>0,'全端全收'=>0,
        //'全端两分以上全播'=>0,'全端两分以上全收'=>0,
        //'全端两分以下全播'=>0,'全端两分以下全收'=>0,
        'WEB全播'=>0,'WEB全收'=>0,
        'WEB两分以上全播'=>0,'WEB两分以上全收'=>0,
        'WEB两分以下全播'=>0,'WEB两分以下全收'=>0,
        'WAP全播'=>0,'WAP全收'=>0,
        'WAP两分以上全播'=>0,'WAP两分以上全收'=>0,
        'WAP两分以下全播'=>0,'WAP两分以下全收'=>0,
        'APP全播'=>0,'APP全收'=>0,
        'APP两分以上全播'=>0,'APP两分以上全收'=>0,
        'APP两分以下全播'=>0,'APP两分以下全收'=>0,
    );
    protected $uidVV = array();
    
    protected $uidAdVVDefault = array(
        'uid'=>0, 
        //'全端效播'=>0,'全端效收'=>0,
        //'全端两分以上效播'=>0,'全端两分以上效收'=>0,
        //'全端两分以下效播'=>0,'全端两分以下效收'=>0,
        'WEB效播'=>0,'WEB效收'=>0,
        'WEB两分以上效播'=>0,'WEB两分以上效收'=>0,
        'WEB两分以下效播'=>0,'WEB两分以下效收'=>0,
        'WAP效播'=>0,'WAP效收'=>0,
        'WAP两分以上效播'=>0,'WAP两分以上效收'=>0,
        'WAP两分以下效播'=>0,'WAP两分以下效收'=>0,
        'APP效播'=>0,'APP效收'=>0,
        'APP两分以上效播'=>0,'APP两分以上效收'=>0,
        'APP两分以下效播'=>0,'APP两分以下效收'=>0,
    );
    protected $uidAdVV = array();
    
    public function __construct(){
        @mkdir("data/upload_uid", 0777, true);
        @mkdir("data/video_uid", 0777, true);
        file_put_contents("data/upload_uid/total.csv", implode(',', array_keys($this->uidVVDefault))."\r\n");
        file_put_contents("data/video_uid/total.csv",  implode(',', array_keys($this->uidVVDefault))."\r\n");
        file_put_contents("data/upload_uid/ad-total.csv", implode(',', array_keys($this->uidAdVVDefault))."\r\n");
        file_put_contents("data/video_uid/ad-total.csv",  implode(',', array_keys($this->uidAdVVDefault))."\r\n");
        
        //额外算的分时间段发布的视频的播放数据
        @mkdir("data/upload_uid_before", 0777, true);//2015十二月份以前
        @mkdir("data/upload_uid_201512", 0777, true);//2015十二月份
        @mkdir("data/upload_uid_201601", 0777, true);//一月份
        @mkdir("data/upload_uid_201602", 0777, true);//二月份
        @mkdir("data/upload_uid_201603", 0777, true);//三月份
        @mkdir("data/upload_uid_201604", 0777, true);//四月份
        @mkdir("data/upload_uid_201605", 0777, true);//五月份
        @mkdir("data/upload_uid_201606", 0777, true);//六月份
        @mkdir("data/upload_uid_201607", 0777, true);//七月份
        @mkdir("data/upload_uid_201608", 0777, true);//八月份
        @mkdir("data/upload_uid_201609", 0777, true);//九月份
        @mkdir("data/upload_uid_201610", 0777, true);//十月份
        @mkdir("data/upload_uid_201611", 0777, true);//十一月份
        file_put_contents("data/upload_uid_before/total.csv", implode(',', array_keys($this->uidVVDefault))."\r\n");
        file_put_contents("data/upload_uid_201512/total.csv", implode(',', array_keys($this->uidVVDefault))."\r\n");
        file_put_contents("data/upload_uid_201601/total.csv", implode(',', array_keys($this->uidVVDefault))."\r\n");
        file_put_contents("data/upload_uid_201602/total.csv", implode(',', array_keys($this->uidVVDefault))."\r\n");
        file_put_contents("data/upload_uid_201603/total.csv", implode(',', array_keys($this->uidVVDefault))."\r\n");
        file_put_contents("data/upload_uid_201604/total.csv", implode(',', array_keys($this->uidVVDefault))."\r\n");
        file_put_contents("data/upload_uid_201605/total.csv", implode(',', array_keys($this->uidVVDefault))."\r\n");
        file_put_contents("data/upload_uid_201606/total.csv", implode(',', array_keys($this->uidVVDefault))."\r\n");
        file_put_contents("data/upload_uid_201607/total.csv", implode(',', array_keys($this->uidVVDefault))."\r\n");
        file_put_contents("data/upload_uid_201608/total.csv", implode(',', array_keys($this->uidVVDefault))."\r\n");
        file_put_contents("data/upload_uid_201609/total.csv", implode(',', array_keys($this->uidVVDefault))."\r\n");
        file_put_contents("data/upload_uid_201610/total.csv", implode(',', array_keys($this->uidVVDefault))."\r\n");
        file_put_contents("data/upload_uid_201611/total.csv", implode(',', array_keys($this->uidVVDefault))."\r\n");
        file_put_contents("data/upload_uid_before/ad-total.csv", implode(',', array_keys($this->uidAdVVDefault))."\r\n");
        file_put_contents("data/upload_uid_201512/ad-total.csv", implode(',', array_keys($this->uidAdVVDefault))."\r\n");
        file_put_contents("data/upload_uid_201601/ad-total.csv", implode(',', array_keys($this->uidAdVVDefault))."\r\n");
        file_put_contents("data/upload_uid_201602/ad-total.csv", implode(',', array_keys($this->uidAdVVDefault))."\r\n");
        file_put_contents("data/upload_uid_201603/ad-total.csv", implode(',', array_keys($this->uidAdVVDefault))."\r\n");
        file_put_contents("data/upload_uid_201604/ad-total.csv", implode(',', array_keys($this->uidAdVVDefault))."\r\n");
        file_put_contents("data/upload_uid_201605/ad-total.csv", implode(',', array_keys($this->uidAdVVDefault))."\r\n");
        file_put_contents("data/upload_uid_201606/ad-total.csv", implode(',', array_keys($this->uidAdVVDefault))."\r\n");
        file_put_contents("data/upload_uid_201607/ad-total.csv", implode(',', array_keys($this->uidAdVVDefault))."\r\n");
        file_put_contents("data/upload_uid_201608/ad-total.csv", implode(',', array_keys($this->uidAdVVDefault))."\r\n");
        file_put_contents("data/upload_uid_201609/ad-total.csv", implode(',', array_keys($this->uidAdVVDefault))."\r\n");
        file_put_contents("data/upload_uid_201610/ad-total.csv", implode(',', array_keys($this->uidAdVVDefault))."\r\n");
        file_put_contents("data/upload_uid_201611/ad-total.csv", implode(',', array_keys($this->uidAdVVDefault))."\r\n");
    }
    
    public function calc(array $uids){
        $count = count($uids);
        $num = 0;
        foreach($uids as $v){
            $ratio = array();
            //print_r(explode(',', trim($v)));echo"<br>";
            list($uid, $ratio['web'], $ratio['wap'], $ratio['app']) = explode(',', trim($v));
            $this->uidRatioMap[$uid] = $ratio;
            $num++;
            echo "uid:{$uid} {$num}/{$count}\r\n";          
            $this->calcByUid($uid);
        }
    }
    
    public function calcByUid($uid){
        $ret = @$this->curlGet(GET_VIDS_URL.$uid);
        $json = json_decode($ret, true);
                
        $this->calcVVByUid($uid, $json, 'upload_uid');
        $this->calcVVByUid($uid, $json, 'video_uid');
        $this->calcVVByUid($uid, $json, 'upload_uid_before');
        $this->calcVVByUid($uid, $json, 'upload_uid_201512');
        $this->calcVVByUid($uid, $json, 'upload_uid_201601');
        $this->calcVVByUid($uid, $json, 'upload_uid_201602');
        $this->calcVVByUid($uid, $json, 'upload_uid_201603');
        $this->calcVVByUid($uid, $json, 'upload_uid_201604');
        $this->calcVVByUid($uid, $json, 'upload_uid_201605');
        $this->calcVVByUid($uid, $json, 'upload_uid_201606');
        $this->calcVVByUid($uid, $json, 'upload_uid_201607');
        $this->calcVVByUid($uid, $json, 'upload_uid_201608');
        $this->calcVVByUid($uid, $json, 'upload_uid_201609');
        $this->calcVVByUid($uid, $json, 'upload_uid_201610');
        $this->calcVVByUid($uid, $json, 'upload_uid_201611');
    }
    
    protected function calcVVByUid($uid, $json, $uidFlag='upload_uid'){
        $csvUidFile = "data/{$uidFlag}/{$uid}.csv";   
        $csvUidAdFile = "data/{$uidFlag}/ad-{$uid}.csv";   
        @unlink($csvUidFile);
        @unlink($csvUidAdFile);
        file_put_contents($csvUidFile, '');
        file_put_contents($csvUidAdFile, '');
        
        $this->uidVV = $this->uidVVDefault;
        $this->uidAdVV = $this->uidAdVVDefault;
        $this->uidVV['uid'] = $uid;
        $this->uidAdVV['uid'] = $uid;
        
        $count = count($json[$uidFlag]);
        $num = 0;
        foreach($json[$uidFlag] as $v){
            $num++;
            echo "uid:{$uid} vid: {$v['vid']} {$uidFlag} {$num}/{$count}\r\n";
            $vv = $this->getVV($v['vid']);
            $this->getVidDetail($vv, $uid, $v['vid'], $v['video_duration'], $csvUidFile);
            $this->getVidDetail($vv, $uid, $v['vid'], $v['video_duration'], $csvUidAdFile, true);
        }
        
        $str = implode(',', $this->uidVV)."\r\n";
        $aDstr = implode(',', $this->uidAdVV)."\r\n";
        file_put_contents("data/{$uidFlag}/total.csv", $str, FILE_APPEND);
        file_put_contents("data/{$uidFlag}/ad-total.csv", $aDstr, FILE_APPEND);
    }
    
    protected function getVidDetail($vv, $uid, $vid, $duration, $csvFile, $isAd = false){
        echo "memory_get_usage()=".memory_get_usage().", memory_get_peak_usage()=".memory_get_peak_usage()."\r\n";
        $data = array();
        $data['vid'] = $vid;
        $data['duration'] = $duration;
        //$vidtotal_all = 0;
        $vidtotal_web = 0;
        $vidtotal_wap = 0;
        $vidtotal_app = 0;        
        for($i=START_TIME; $i<=END_TIME; $i++){
            //$data[$i] = $isAd ? (intval($vv[$i]['adplay_num'])+intval($vv[$i]['load_wap_num'])+intval($vv[$i]['load_app_num'])) : ($vv[$i]['load_num']?:0);//总有效播放不再取自adplay_num，而是adplay_num+load_wap_num+load_app_num
            //$vidtotal_all += $data[$i];
            $numKeyWeb = $isAd ? 'adplay_num' : 'web_load_num';//WEB有效播放量暂时等于adplay_play
            $data[$i.'_web']= isset($vv[$i][$numKeyWeb]) ? $vv[$i][$numKeyWeb] : 0;
            $vidtotal_web += $data[$i.'_web'];
            $numKeyWap = 'wap_load_num';//WAP有效播放暂时取自WAP全播
            $data[$i.'_wap']= isset($vv[$i][$numKeyWap]) ? $vv[$i][$numKeyWap] : 0;
            $vidtotal_wap += $data[$i.'_wap'];
            $numKeyApp = 'app_load_num';//APP有效播放暂时取自APP全播
            $data[$i.'_app']= isset($vv[$i][$numKeyApp]) ? $vv[$i][$numKeyApp] : 0;
            $vidtotal_app += $data[$i.'_app'];
        }

        $data['vidtotal_web'] = $vidtotal_web;
        $data['vidtotal_wap'] = $vidtotal_wap;
        $data['vidtotal_app'] = $vidtotal_app;
        
        $adVarTag = $isAd ? 'Ad' : '';
        $adWordFlag = $isAd ? '效' : '全';
        $totalPlatformSufix = array('WEB'=>'web','WAP'=>'wap','APP'=>'app');
        foreach ($totalPlatformSufix as $platform => $suffix) {
            foreach (array('', ($duration>=120) ? '两分以上' : '两分以下') as $durationFlag) {
                $this->{"uid{$adVarTag}VV"}["{$platform}{$durationFlag}{$adWordFlag}播"] += ${"vidtotal_{$suffix}"};
                $this->{"uid{$adVarTag}VV"}["{$platform}{$durationFlag}{$adWordFlag}收"] += ${"vidtotal_{$suffix}"} * $this->uidRatioMap[$uid][$suffix];
            }
        }
        $str = implode(',', $data)."\r\n";
        file_put_contents($csvFile, $str, FILE_APPEND);
    }

    protected function getVV($vid){
        $ret = @$this->curlGet(GET_VV_URL.$vid);
        $json = json_decode($ret, true);
        return $json['result']['list']['byDate'];        
    }
    
    //旧版：作废原因：没必要缓存，不会用到第二次。防止爆内存
    protected function getVV_OLD($vid){
        //echo "getVV(): vid = ".$vid.", isset = ".(isset($static[$vid])?1:0)."\r\n";
        static $static;
        if( !isset($static[$vid]) ){
            $ret = @$this->curlGet(GET_VV_URL.$vid);
            $json = json_decode($ret, true);
            $static[$vid] = $json['result']['list']['byDate'];
        }
        //print_r($static[$vid]);//test
        
        return $static[$vid];
    }
    
	protected function curlGet($url, $timeout=10) {
		do{
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            $result = curl_exec($ch);        
            curl_close($ch);
        }while( empty($result) );
        
		return $result;
	}
}