<?php
date_default_timezone_set('PRC');
ini_set("display_errors", "On");
error_reporting(E_ALL & ~E_STRICT);
/**
 * ���������ű������������¿�ʼ�ļ���
 */
mb_internal_encoding("GB2312");//���ļ����б��뻷��Ϊ��������

define('START_TIME', 20170201);
define('END_TIME', 20170228);
define('GET_VIDS_URL', "http://v.huya.com/?r=test/GetVidByUid&calcMore=1&uid=");//��ȡvid��duration�����ȡ���µ����ݣ�
define('GET_VV_URL', "http://playstats-manager.v.duowan.com/?r=api/getAdPlay&startTime=".START_TIME."&endTime=".END_TIME."&vid="); 

$uids = file("uids.txt");
$obj = new fengcheng();
$obj->calc($uids);

class fengcheng{
    
    protected $uidRatioMap = array();//[uid] => array('web'=>0.003, 'wap'=>0.0015, 'app'=>0.0015)
    
    protected $uidVVDefault = array(
        'uid'=>0, 
        //'ȫ��ȫ��'=>0,'ȫ��ȫ��'=>0,
        //'ȫ����������ȫ��'=>0,'ȫ����������ȫ��'=>0,
        //'ȫ����������ȫ��'=>0,'ȫ����������ȫ��'=>0,
        'WEBȫ��'=>0,'WEBȫ��'=>0,
        'WEB��������ȫ��'=>0,'WEB��������ȫ��'=>0,
        'WEB��������ȫ��'=>0,'WEB��������ȫ��'=>0,
        'WAPȫ��'=>0,'WAPȫ��'=>0,
        'WAP��������ȫ��'=>0,'WAP��������ȫ��'=>0,
        'WAP��������ȫ��'=>0,'WAP��������ȫ��'=>0,
        'APPȫ��'=>0,'APPȫ��'=>0,
        'APP��������ȫ��'=>0,'APP��������ȫ��'=>0,
        'APP��������ȫ��'=>0,'APP��������ȫ��'=>0,
    );
    protected $uidVV = array();
    
    protected $uidAdVVDefault = array(
        'uid'=>0, 
        //'ȫ��Ч��'=>0,'ȫ��Ч��'=>0,
        //'ȫ����������Ч��'=>0,'ȫ����������Ч��'=>0,
        //'ȫ����������Ч��'=>0,'ȫ����������Ч��'=>0,
        'WEBЧ��'=>0,'WEBЧ��'=>0,
        'WEB��������Ч��'=>0,'WEB��������Ч��'=>0,
        'WEB��������Ч��'=>0,'WEB��������Ч��'=>0,
        'WAPЧ��'=>0,'WAPЧ��'=>0,
        'WAP��������Ч��'=>0,'WAP��������Ч��'=>0,
        'WAP��������Ч��'=>0,'WAP��������Ч��'=>0,
        'APPЧ��'=>0,'APPЧ��'=>0,
        'APP��������Ч��'=>0,'APP��������Ч��'=>0,
        'APP��������Ч��'=>0,'APP��������Ч��'=>0,
    );
    protected $uidAdVV = array();
    
    public function __construct(){
        @mkdir("data/upload_uid", 0777, true);
        @mkdir("data/video_uid", 0777, true);
        file_put_contents("data/upload_uid/total.csv", implode(',', array_keys($this->uidVVDefault))."\r\n");
        file_put_contents("data/video_uid/total.csv",  implode(',', array_keys($this->uidVVDefault))."\r\n");
        file_put_contents("data/upload_uid/ad-total.csv", implode(',', array_keys($this->uidAdVVDefault))."\r\n");
        file_put_contents("data/video_uid/ad-total.csv",  implode(',', array_keys($this->uidAdVVDefault))."\r\n");
        
        //������ķ�ʱ��η�������Ƶ�Ĳ�������
        @mkdir("data/upload_uid_before", 0777, true);//2015ʮ���·���ǰ
        @mkdir("data/upload_uid_201512", 0777, true);//2015ʮ���·�
        @mkdir("data/upload_uid_201601", 0777, true);//һ�·�
        @mkdir("data/upload_uid_201602", 0777, true);//���·�
        @mkdir("data/upload_uid_201603", 0777, true);//���·�
        @mkdir("data/upload_uid_201604", 0777, true);//���·�
        @mkdir("data/upload_uid_201605", 0777, true);//���·�
        @mkdir("data/upload_uid_201606", 0777, true);//���·�
        @mkdir("data/upload_uid_201607", 0777, true);//���·�
        @mkdir("data/upload_uid_201608", 0777, true);//���·�
        @mkdir("data/upload_uid_201609", 0777, true);//���·�
        @mkdir("data/upload_uid_201610", 0777, true);//ʮ�·�
        @mkdir("data/upload_uid_201611", 0777, true);//ʮһ�·�
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
            //$data[$i] = $isAd ? (intval($vv[$i]['adplay_num'])+intval($vv[$i]['load_wap_num'])+intval($vv[$i]['load_app_num'])) : ($vv[$i]['load_num']?:0);//����Ч���Ų���ȡ��adplay_num������adplay_num+load_wap_num+load_app_num
            //$vidtotal_all += $data[$i];
            $numKeyWeb = $isAd ? 'adplay_num' : 'web_load_num';//WEB��Ч��������ʱ����adplay_play
            $data[$i.'_web']= isset($vv[$i][$numKeyWeb]) ? $vv[$i][$numKeyWeb] : 0;
            $vidtotal_web += $data[$i.'_web'];
            $numKeyWap = 'wap_load_num';//WAP��Ч������ʱȡ��WAPȫ��
            $data[$i.'_wap']= isset($vv[$i][$numKeyWap]) ? $vv[$i][$numKeyWap] : 0;
            $vidtotal_wap += $data[$i.'_wap'];
            $numKeyApp = 'app_load_num';//APP��Ч������ʱȡ��APPȫ��
            $data[$i.'_app']= isset($vv[$i][$numKeyApp]) ? $vv[$i][$numKeyApp] : 0;
            $vidtotal_app += $data[$i.'_app'];
        }

        $data['vidtotal_web'] = $vidtotal_web;
        $data['vidtotal_wap'] = $vidtotal_wap;
        $data['vidtotal_app'] = $vidtotal_app;
        
        $adVarTag = $isAd ? 'Ad' : '';
        $adWordFlag = $isAd ? 'Ч' : 'ȫ';
        $totalPlatformSufix = array('WEB'=>'web','WAP'=>'wap','APP'=>'app');
        foreach ($totalPlatformSufix as $platform => $suffix) {
            foreach (array('', ($duration>=120) ? '��������' : '��������') as $durationFlag) {
                $this->{"uid{$adVarTag}VV"}["{$platform}{$durationFlag}{$adWordFlag}��"] += ${"vidtotal_{$suffix}"};
                $this->{"uid{$adVarTag}VV"}["{$platform}{$durationFlag}{$adWordFlag}��"] += ${"vidtotal_{$suffix}"} * $this->uidRatioMap[$uid][$suffix];
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
    
    //�ɰ棺����ԭ��û��Ҫ���棬�����õ��ڶ��Ρ���ֹ���ڴ�
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