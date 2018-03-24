<?php
/**
 * 过程锁实用类
 */
class OpLock {

    static $pool = array();//锁池

    static $mmc = null;//通用缓存实例

    protected $_currKey = '';//当前锁的key

    public function __construct($key = null){
        $this->_currKey = $key;
        if (empty(self::$mmc)) {
            self::$mmc = obj('dwCache', array(__CLASS.__FUNCTION__), '', true);
        }
    }

    //获取一个锁的句柄
    public function inst($key){
        if (empty($key) && ! is_numeric($key)) return false;
        $key = sha1($key);
        if (! isset(self::$pool[$key])) {
            self::$pool[$key] = new self($key);
        }
        return self::$pool[$key];
    }

    public function isLocked(){
        $key = $this->_currKey;
        if (null == $key) throw new Exception('key is not found!');
        $cache = self::$mmc->get($key);
        return $cache == 1;
    }

    public function lock(){
        $key = $this->_currKey;
        if (null == $key) throw new Exception('key is not found!');
        return self::$mmc->set($key, 1);
    }

    public function unlock(){
        $key = $this->_currKey;
        if (null == $key) throw new Exception('key is not found!');
        return self::$mmc->delete($key);
    }

    //为一个过程加锁，body为需要加锁的过程。仅onException可包含结束程序的语句
    public function promise(array $args){
        $body = $args['body'] ?: function(){};
        $onLocked = $args['onLocked'] ?: function(){};
        $onException = $args['onException'] ?: function(Exception $e){};
        if ($this->isLocked()) {
            call_user_func($onLocked);
            return;
        }
        $this->lock();
        try {
            call_user_func($body);
            $this->unlock();
        } catch (Exception $e) {
            $this->unlock();
            call_user_func($onException, $e);
        }
    }

}