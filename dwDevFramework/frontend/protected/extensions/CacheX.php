<?php
require_once(BASE_DIR . 'protected/extensions/Cache.php');

/**
 * 缓存控制-改良版
 * 可以通过fluXxx方法灵活操作任意目标缓存
 * 
 * 这里所有方法的采用可变参数，意义如下：
 *      第一个参数作为缓存控制句柄
 *      第一个参数加上其余参数，作为完整的数据存取句柄
 *      控制句柄与存取句柄的区别在于：[控制句柄]是[存取句柄]的字符串子集，使用[控制句柄]可以轻松控制特定的一批缓存，而[存取句柄]则控制起来不是很灵活
 * 参数使用技巧：
 *      如果需要将多个参数作为实际句柄，则可将多个参数拼成一个字符串或数组，建议用数组，意义更明。
 */
class CacheX extends Cache {

    //刷新[控制句柄]所制定的一批缓存，故flu仅使用方法的第一个参数，例如 fluXxx('a'), fluXxx([1, 'a'])
    protected function _flu($cacheType, $methodName, $params){
        list ($ctrlH, $ctrlVer) = $this->_getCtrlVer($cacheType, $methodName, $params);
        if ($cacheType == 'redis') {
            obj('dwRedis')->setex($ctrlH, 86400*7, $ctrlVer + 1);
        } else {
            obj('dwCache')->set($ctrlH, $ctrlVer + 1, 86400*7);
        }
    }


    protected function _getHashKey($cacheType, $methodName, $params){
        $hashKey = $this->cacheMap[$methodName]['ver'].'_'.md5(json_encode($params));
        list ($ctrlH, $ctrlVer) = $this->_getCtrlVer($cacheType, $methodName, $params);
        return $hashKey.$ctrlVer;
    }


    protected function _getCtrlVer($cacheType, $methodName, $params){
        $ctrlH = md5(json_encode([$methodName, $params[0]]));
        if ($cacheType == 'redis') {
            $ctrlVer = obj('dwRedis')->get($ctrlH) ?: mt_rand(1, 999);
            obj('dwRedis')->setex($ctrlH, 86400*7, $ctrlVer);
        } else {
            $ctrlVer = obj('dwCache')->get($ctrlH) ?: mt_rand(1, 999);
            obj('dwCache')->set($ctrlH, $ctrlVer, 86400*7);
        }
        return [$ctrlH, $ctrlVer];
    }


    public function __call($method, $params){
        $method = strtolower($method);
        $op = substr($method, 0, 3);
        $methodName = substr($method, 3);
        
        if( !in_array($op, array('get', 'set', 'del', 'exp', 'flu')) ){
            throw new Exception("'{$method}' method must begin with 'get', 'set', 'del','exp', 'flu'");
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
            
        $cacheType = $this->cacheMap[$methodName]['type'];
        $hashKey = $this->_getHashKey($cacheType, $methodName, $params);
        
        if( 'get'==$op && CACHE_GET_ABLE){
            if( !empty($this->cacheMap[$methodName]['expire_special']) ){
                $expire = $this->cacheMap[$methodName]['expire_special'];
                $this->cacheMap[$methodName]['expire_special'] = 0;
            }else{
                $expire = $this->cacheMap[$methodName]['expire'] - mt_rand(0, 10);
            }
            return $this->_get($cacheType, $methodName, $hashKey, $expire);   
        }else if( 'set'==$op && CACHE_SET_ABLE ){
            return $this->_set($cacheType, $methodName, $hashKey, $data);   
        }else if( 'del'==$op ){
            return $this->_del($cacheType, $methodName, $hashKey);
        }else if( 'exp'==$op ){
            return $this->cacheMap[$methodName]['expire_special'] = intval($params[0]);
        }else if( 'flu'==$op ){
            return $this->_flu($cacheType, $methodName, $params);
        }else{
            return NULL;
        }
    }

}