<?php
/**
 * 通用缓存控制
 * 代码取自多玩视频项目，并作轻微改动。
 *      不再使用常量CACHE_GET_ABLE和CACHE_SET_ABLE，
 *      而是采用 $GLOBALS['cache_control']['CACHE_GET_ABLE'] 和 $GLOBALS['cache_control']['CACHE_SET_ABLE']
 */
class Cache {	         
    protected $cacheMap = array(
        'OpLock' =>  array('ver'=>'1', 'expire'=>86400,  'type'=>'redis'), //操作锁
        'Other' =>  array('ver'=>'1', 'expire'=>86400,  'type'=>'redis'), //未归类的缓存
    );
    
    public function __construct(){
        //统一转成小写处理
        foreach($this->cacheMap as $k=>$v){
            $k = strtolower($k);
            $this->cacheMap[$k] = $v; 
        }           
    }
    
    public function getMemCache(){
        static $cacheObj;
        if( !$cacheObj ){ //单例
            $cacheObj = obj('dwCache', array($GLOBALS['app_id']), '', true);
        }
        return $cacheObj;
    }
    
    public function getRedis(){
        static $cacheObj;
        if( !$cacheObj ){  //单例
            $cacheObj= obj('dwRedis');
        }
        return $cacheObj;
    }

    public function call($callMethod, $params, $cacheMethod = 'other'){
       list($class, $method) = explode('::', $callMethod);
       $cacheParams = $params;
       if( 'other'==$cacheMethod ){
           $key = strtolower($class.'_'.$method);
           array_unshift($cacheParams, $key);
       }
       
       //获取缓存
       $cacheGetMethod = 'get'.$cacheMethod;
       $cache = call_user_func_array(array($this, $cacheGetMethod), $cacheParams);
       if( $cache ) return $cache;
       
       //获取数据
       $result = call_user_func_array(array(obj($class), $method), $params);
       if( false=== $result ) return $result;
       
       //写入缓存
       array_push($cacheParams, $result);
       $cacheSetMethod = 'set'.$cacheMethod;
       call_user_func_array(array($this, $cacheSetMethod), $cacheParams);
       
       return $result;
    }
    
    public function __call($method, $params){
        $method = strtolower($method);
        $op = substr($method, 0, 3);
        $methodName = substr($method, 3);
        
        if( !in_array($op, array('get', 'set', 'del', 'exp')) ){
            throw new Exception("'{$method}' method must begin with 'get', 'set', 'del','exp'");
        }     
        if( !isset($this->cacheMap[$methodName]) ){
            throw new Exception("'{$method}' method does not exist");
        }
                
        if( 'set'==$op ) $data = array_pop($params);
        if( 0==count($params) ){
            throw new Exception("cache params error");
        }
        
        foreach ($params as $k => $v) {
            if (is_numeric($v)) $params[$k] = strval($v);//数字参数最终处理为字符串
        }
            
        $hashKey = $this->cacheMap[$methodName]['ver'].'_'.md5(json_encode($params));      
        $cacheType = $this->cacheMap[$methodName]['type'];
        
        if( 'get'==$op && $GLOBALS['cache_control']['CACHE_GET_ABLE']){
            if( !empty($this->cacheMap[$methodName]['expire_special']) ){
                $expire = $this->cacheMap[$methodName]['expire_special'];
                $this->cacheMap[$methodName]['expire_special'] = 0;
               
            }else{
                $expire = $this->cacheMap[$methodName]['expire'] - mt_rand(0, 10);
            }
        
            return $this->_get($cacheType, $methodName, $hashKey, $expire);   
        }else if( 'set'==$op && $GLOBALS['cache_control']['CACHE_SET_ABLE'] ){
            return $this->_set($cacheType, $methodName, $hashKey, $data);   
        }else if( 'del'==$op ){
            return $this->_del($cacheType, $methodName, $hashKey);
        }else if( 'exp'==$op ){
            return $this->cacheMap[$methodName]['expire_special'] = intval($params[0]);
        }else{
            return NULL;
        }
    }

    protected function _get($cacheType, $methodName, $hashKey, $expire){
        if( 'redis'==$cacheType ){
            $value = $this->getRedis()->hGet($methodName, $hashKey);
            $value = @json_decode($value, true);
        }else{
            $value = $this->getMemCache()->get($methodName.'_'.$hashKey);
        }
        //不论用memcache还是redis，第一次取数据时$value['data']的非严格值为NULL，故用更靠谱的判断【NULL!==$value['data']】代替【!empty($value['data'])】
        if( NULL!==$value['data'] && ($value['time']+$expire)>time() ){
            return $value['data'];
        }
        return NULL;
    }

    protected function _set($cacheType, $methodName, $hashKey, $data){
        $value = array('data'=>$data, 'time'=>time());
        if( 'redis'==$cacheType ){
            $value = json_encode( $value );
            return $this->getRedis()->hSet($methodName, $hashKey, $value);
        }else{
            return $this->getMemCache()->set($methodName.'_'.$hashKey, $value, 86400); 
        }
    }   

    protected function _del($cacheType, $methodName, $hashKey){
        if( 'redis'==$cacheType ){
            return $this->getRedis()->hDel($methodName, $hashKey);
        }else{
            return $this->getMemCache()->delete($methodName.'_'.$hashKey);
        }
    }
}