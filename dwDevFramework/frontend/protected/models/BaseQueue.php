<?php

class BaseQueue {

    protected $keyPre;
    protected $cliCookieName;
    protected $queueNum = 1;//默认不拆分队列

    public function report(array $data, $consumeTimes = 1, $consumeDelay = 120){
        $key = $this->pickQueueKey();
        obj('dwRedis')->rPush($key, json_encode([
            'data' => $data,
            'consumeTimes' => $consumeTimes,//总消费次数
            'consumeRemainTimes' => $consumeTimes,//剩余消费次数
            'consumeDelay' => $consumeDelay,//重复消费间隔时间（秒）
            'time' => time(),
            'refer' => $_SERVER['HTTP_REFERER'] ?: '',
            'cli' => $this->getCli(),
            'ip' => getIP(),
            'debug' => [
                'report_uri' => $_SERVER['REQUEST_URI'],
                'request' => $_REQUEST,
                'post' => $_POST,
                'get' => $_GET,
                'ua' => $_SERVER['HTTP_USER_AGENT'],
            ],
        ]));
    }


    //随机选取一个队列（前后台代码保持一致）
    protected function pickQueueKey(){
        $keys = $this->getQueueKeys();
        return $keys[mt_rand(0, count($keys)-1)];
    }


    //获取分批队列（前后台代码保持一致）
    protected function getQueueKeys(){
        $this->queueNum = (int) $this->queueNum;
        if ($this->queueNum < 1 || $this->queueNum > 50) {
            throw new Exception('队列数超限[1~50]');
        }
        if (empty($this->keyPre)) {
            throw new Exception('必须指定队列key前缀');
        }
        $keys = [];
        for ($i = 0; $i < $this->queueNum; $i ++) {
            $keys[] = $this->keyPre . '_' . $i;
        }
        return $keys;
    }


    //设置客户端标识
    protected function getCli(){
        if (empty($this->cliCookieName)) {
            throw new Exception("必须指定客户端标识专用的cookie名");
        }
        if (! isset($_COOKIE[$this->cliCookieName])) {
            // getIP();  @todo 限制同个IP不能生成太多客户端标识，具体待定
            $cli = sha1(microtime(1).mt_rand(0, 9999));
            setcookie($this->cliCookieName, $cli, time()+86400*30, '/');
            return $cli;
        } else {
            return $_COOKIE[$this->cliCookieName];
        }
    }


    //工具：查看队列使用情况
    public function queueStatus($detail = false){
        $redis = obj('dwRedis');
        $keys = $this->getQueueKeys();
        foreach ($keys as $key) {
            $len = $redis->lLen($key);
            echo "Queue: {$key}, len = ".$len."\n";
            if ($detail) {
                echo "10 of tail: \n";
                $tailList = $redis->lRange($key, $len>=10?-10:0, -1);
                foreach ($tailList as $v) {
                    echo " >> {$v} \n";
                }
            }
            echo "--------------------------\n";
        }
    }

}