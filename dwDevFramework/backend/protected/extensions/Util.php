<?php

class Util{

	//通过yyuid获得昵称
	public function getNickname($uid){
		//取缓存
		$cacheKey = 'yy_nickname_'.$uid;
		$nickname = obj('dwCache', array('huya_nickname'))->get($cacheKey);
		if( $nickname ) return $nickname;
		
		$url = 'http://webapi.duowan.com/yy_to_uid/api_uidtonick.php?uid='.$uid;
		$result = '';
		for($i=0; $i<3; $i++){
			$result = obj('dwHttp')->get($url);
       	 	if( !empty($result) && strlen($result)>10 ){
				break;
			}
		}
		$result = json_decode($result, 1);
		if( !empty($result['code']) && 1==$result['code'] ){
			if( empty( $result['data']) && !empty($result['base64_data']) ){
				 $result['data'] = base64_decode($result['base64_data']);
			}
			//更新缓存
			obj('dwCache', array('5153_nickname'))->set($cacheKey, $result['data'], 3600*24);
			return $result['data'];
		}
		return '';
	}


    //通过yyuid获取昵称（解决正整数二进制溢出32位的问题）
    public function getNicknameByUDBProxy(array $cookies = []){
        $info = $this->getInfoByUDBProxy($cookies);
        return $info['nickname'] ?: $info['username'] ?: '多玩网友';
    }


    /**
     * 获取新版UDB信息，返回格式：
        data.yyuid	            long	N	yyuid
        data.username	        string	N	通行证名称，多玩新版则是多玩自己生成的
        data.nickname	        string	Y	昵称，不存在，则为空值
        data.avatar	            string	Y	头像，不存在，则为空值
        data.is_realname	    int	    N	是否实名，1实名，0未实名
        data.bindList	        array	Y	已绑定的第三方帐号列表
        data.bindList.openid	string		第三方帐号openid，手机类型的openid存储和输出为手机号的md5值
        data.bindList.type	    string		第三方帐号类型
        data.bindList.bindTime	date		绑定时间
     *
     * @param array $cookies
     */
    public function getInfoByUDBProxy(array $cookies = []){
        if (! isset($GLOBALS['api']['udbproxy']['getInfo'])) {
            throw new Exception('GLOBALS没有声明[api][udbproxy][getInfo]');
        }
        $setup = $GLOBALS['api']['udbproxy']['getInfo'];
        if (empty($cookies)) $cookies = $_COOKIE;
        $url = $setup['url'];
        $params = [
            'appid' => $setup['appid'],
            'yyuid' => $cookies['lg_uid'] ?: $cookies['yyuid'],
            'openid' => $cookies['lg_openid'],
            'type' => $cookies['lg_type'],
            'force' => 0,
        ];
        $params['sign'] = md5($params['appid'] . $setup['secret'] . $params['yyuid'] . $params['openid'] . $params['type'] . $params['force']);
        $ret = obj('dwHttp')->post($url, $params);
        @$ret = json_decode($ret?:'[]', 1);
        if ($ret['code'] != 1) {
            throw new Exception("获取UDB信息失败, 提示信息[{$ret['msg']}]");
        }
        return $ret['data'];
    }

	
	/**
	 * 比较完善的XSS过滤器(支持单个或批量)
	 * @param array $items 待处理数组，引用变量。若传入字符串，则当做单个来处理。
	 * @param bool $fullfilter 默认处理数组全部元素
	 * @param array $needKeys 需要处理的元素所在的键，该参数仅在$fullfilter=false时有效
	 */
	public function xssFilter(&$items = array(), $fullfilter = true, $needKeys = array()){
	    $filter = function(&$v){
            $v = obj('XssHtml', array($v), '', true)->getHtml();
            $v = preg_replace(array('/\\n\<p\>/is', '/\<\/p\>\\n/is'), '', $v);//作者已修复对纯文本自动换行和加p标签包裹的BUG，此行代码在观测稳定后再删除
            
	    };
	    if (is_string($items)) {
	        $filter($items);
	        return;
	    }
        foreach ($items as $k => &$v) {
            if ($fullfilter || in_array($k, $needKeys) && is_string($v)) {
                $filter($v);
            }
	    }
	}


    /**
     * 重复调用（缓存于本次请求的内存中）
     * 用法同call_user_func_array()
     * 参数$useCache默认使用本次请求中的结果缓存
     */
    public function recall($callback, $param_arr, $useCache = true){
        $key = 'recall_ret_' . sha1( ( is_array($callback) ? get_class($callback[0]).$callback[1] : json_encode($callback) ) . serialize($param_arr) );
        $cache = $useCache ? $GLOBALS[$key] : false;
        if ($cache) return $cache;
        $ret = call_user_func_array($callback, $param_arr);
        $GLOBALS[$key] = $ret;
        return $ret;
    }
    
    /**
     * 带有缓存的调用（缓存于全局）
     * 仅适用于返回值一直依赖参数变化的方法
     */
    public function callCache($callback, $param_arr, $mmcKey = false, $expire = 600){
        $trace = debug_backtrace();
        static $mmc = null;
        $mmc = $mmc ?: obj('dwCache', array($GLOBALS['app_id']), '', true); //确保使用与外界不同的实例，但防止此作用域重复生成实例
        false !== $mmcKey || $mmcKey = 'callcache_ret_' . sha1( ( is_array($callback) ? get_class($callback[0]).$callback[1] : json_encode($callback) ) . serialize($param_arr) . $trace[1]['file'].$trace[1]['line'] );
        $cache = $mmc->get($mmcKey);
        if ($cache && CACHE_GET_ABLE) return $cache;
        $ret = call_user_func_array($callback, $param_arr);
        if (CACHE_SET_ABLE) $mmc->set($mmcKey, $ret, $expire);
        return $ret;
    }
    
    /**
     * 带有缓存的调用（缓存于全局）
     * 仅适用于返回值一直依赖参数变化的方法
     */
    public function callRedis($callback, $param_arr, $redisKey = false, $expire = 600){
        $trace = debug_backtrace();
        static $redis = null;
        $redis = $redis ?: obj('dwRedis');
        false !== $redisKey || $redisKey = 'callredis_ret2_' . sha1( ( is_array($callback) ? get_class($callback[0]).$callback[1] : json_encode($callback) ) . serialize($param_arr) . $trace[1]['file'].$trace[1]['line'] );
        $cache = $redis->get($redisKey);
        if ($cache && CACHE_GET_ABLE) return unserialize($cache);
        $ret = call_user_func_array($callback, $param_arr);
        if (CACHE_SET_ABLE) $redis->set($redisKey, serialize($ret), $expire);
        return $ret;
    }
    
    
    /**
     * 用打乱顺序、随机长度的ASCII码字符串来生成随机字符串
     */
    public function buildRandStr(){
        $str = '';
        $len = mt_rand(10, 255);
        for ($i = 0; $i < $len; $i ++) {
            $str .= chr(mt_rand(0, 255));
        }
        return sha1($str);
    }
    
    /**
     * 获取本周时间信息
     * @param mixed $format 输出格式，默认false输出时间戳；可以设置“Y-m-d”之类参数来控制格式
     * @param int $firstDay 设置为1，则表示周一为每周开始
     */
    public function getCurrentWeek($format = false, $firstDay = 1){
        $today = date('Y-m-d');
        $w = date('w', strtotime($today));
        $weekStart = strtotime("$today -".($w ? $w - $firstDay : 6).' days');
        $weekEnd = $weekStart + 6 * 86400;
        if (!! $format) {
            $weekStart = date($format, $weekStart);
            $weekEnd = date($format, $weekEnd);
        }
        return array('weekIndex' => $w, 'weekStart' => $weekStart, 'weekEnd' => $weekEnd);
    }
    

    //集合相减
    public function substractSet($sLeft, $sRight){
        $sLeft = array_unique($sLeft);
        $sRight = array_unique($sRight);
        natsort($sLeft);
        natsort($sRight);
        foreach ($sRight as $v) {
            $k = array_search($v, $sLeft);
            if (false !== $k) unset($sLeft[$k]);
        }
        return array_values($sLeft);
    }

    //格式化显示debug trace
    public function showTrace($errMsg = '', $goDie = true) {
        $trace = debug_backtrace();
        $out = "<hr/><div>".$errMsg."<br /><table border='1'>";
        $out .= "<thead><tr><th>file</th><th>line</th><th>function</th><th>args</th></tr></thead>";
        foreach ($trace as $k => $v) {
            if ($k == 0) continue;//忽略本方法调用信息
            if (!isset($v['file'])) $v['file'] = '[PHP Kernel]';
            if (!isset($v['line'])) $v['line'] = '';
            $v['args'] = print_r($v['args'], true);
            $out .= "<tr><td>{$v["file"]}</td><td>{$v["line"]}</td><td>{$v["function"]}</td><td><pre>{$v["args"]}</pre></td></tr>";
        }
        $out .= "</table></div><hr/></p>";
        echo $out;
        $goDie AND die();
    }
    

    //UDB转YYUID，支持201712新上的通用第三方登录生成的账号
    public function getUidByUDB($udb){
        $hdl = __CLASS__.__FUNCTION__.$udb;
        $cache = obj('dwCache')->get($hdl);
        if (false !== $cache) {
            return $cache;
        }
        $api = "{$GLOBALS['api']['udbproxy']['getUidByUDB']['url']}?udb={$udb}";
        $ret = obj('dwHttp')->get($api);
        $ret = json_decode($ret?:'[]', 1);
        if (! isset($ret['rs']) || false === $ret['rs']) {
            throw new Exception('fail to get yyuid by udb');
        }
        $uid = $ret['yyuid'];
        obj('dwCache')->set($hdl, $uid, 300);
        return $uid;
    }


    //逗号分隔的字符串转换为数组
    public function commaToArray($str, $delElemSpace = true){
        if ($delElemSpace) { //默认删除每个分割元素中间的空白符
            $str = preg_replace('/\s/', '', $str);
        }
        $arr = array_values(array_unique(array_filter(preg_split('/,|，/', $str))));
        array_walk($arr, function(&$v, $k){
            $v = trim($v);
        });
        return $arr;
    }

    
    //接口加密调用的统一响应，需要参数：jsonData, sign
    public function checkInvokeSign($jsonData, $sign, Closure $onPass = null, Closure $onBlock = null, $key = 'duowan~!@#$%^&*'){
        $jsonData = $jsonData ?: '{}';
        $data = json_decode($jsonData, 1);
        $md5 = md5($jsonData.$key);
        if ($md5 == $sign) {
            $feedback = [
                'rs' => true,
                'msg' => 'check sign success',
            ];
            $onPass AND call_user_func($onPass, $feedback, $data);
        } else {
            $feedback = [
                'rs' => false,
                'msg' => 'check sign fail',
                'debug' => compact('jsonData', 'data', 'sign'),
            ];
            $onBlock AND call_user_func($onBlock, $feedback, $data);
        }
    }


    //yyuid转UDB，考虑溢出32位的情况
    public function uid2username($uid){
        $url = "{$GLOBALS['api']['udbproxy']['getUdbByUid']['url']}?yyuid={$uid}";
        $ret = false;
        for ($i = 0; $i < 3 && false === $ret; $i ++) {
            $ret = obj('dwHttp')->get($url);
        }
        if (false === $ret) {
            return false;
        }
        $ret = json_decode($ret, 1);
        if (1 != $ret['code']) {
            return false;
        }
        return $ret['udb'];
    }

}