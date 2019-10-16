<?php

abstract class BaseQueue {

    protected $keyPre;
    protected $cliCookieName;
    protected $queueNum = 1;//默认不拆分队列
    protected $dayNumByCli = 1000;
    protected $dayNumByIp = 100000;


    //消费队列
    public function consume(){
        $redis = obj('dwRedis');
        $keys = $this->getQueueKeys();
        foreach ($keys as $key) {
            $total = $redis->lLen($key);
            $limit = $total >= 50 ? 50 : $total;
            echo "Queue: {$key}, total={$total}, limit={$limit} \n";
            try {
                $list = $redis->lRange($key, 0, $limit-1);
                $this->handleElemList($list);
                $redis->lTrim($key, $limit, -1);
            } catch (Exception $e) {
                obj('TmpLog')->add($key, $e->getMessage(), __CLASS__.__FUNCTION__);
            }
            echo "---------------------\n";
        }
    }


    //处理队列分片
    protected function handleElemList(array $list){
        foreach ($list as $v) {
            echo "deal elem: {$v}, ";
            $v = json_decode($v, 1);

            if (! $this->checkDayNumByCli($v)) {
                echo "ignore[checkDayNumByCli] \n";
                continue;//限制同一条数据，当天在同客户端执行上报的次数
            }

            if (! $this->checkDayNumByIp($v)) {
                echo "ignore[checkDayNumByIp] \n";
                continue;//限制同一条数据，当天在同IP执行上报的次数
            }

            if ($v['consumeTimes'] != $v['consumeRemainTimes'] && time() < $v['time'] + $v['consumeDelay']) {
                echo "Untimely... \n";
                $this->rePush($v);
                continue;//还没轮到 再次消费 的时机
            }

            $this->handleOne($v);
            $v['consumeRemainTimes'] --;
            if ($v['consumeRemainTimes'] >= 1) {
                $this->rePush($v);
            }
        }
    }


    //未到消费时机，或还有剩余消费次数，则塞回任意队列
    private function rePush($elem){
        obj('dwRedis')->rPush($this->pickQueueKey(), json_encode($elem));
    }


    //处理其中一条出队数据
    abstract protected function handleOne($elem);


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



    //同个客户端每天限1000次
    protected function checkDayNumByCli($elem){
        $date = date('Ymd', $elem['time']);
        $key = sha1(__CLASS__.__FUNCTION__.$date.serialize($elem['data']).$elem['cli'].'v1');
        $num = obj('dwRedis')->get($key) ?: 0;
        if ($num >= $this->dayNumByCli) {
            return false;
        }
        obj('dwRedis')->incr($key);
        obj('dwRedis')->expire($key, 86400);
        return true;
    }


    //同个IP每天限10W次
    protected function checkDayNumByIp($elem){
        $date = date('Ymd', $elem['time']);
        $key = sha1(__CLASS__.__FUNCTION__.$date.serialize($elem['data']).$elem['ip'].'v1');
        $num = obj('dwRedis')->get($key) ?: 0;
        if ($num >= $this->dayNumByIp) {
            return false;
        }
        obj('dwRedis')->incr($key);
        obj('dwRedis')->expire($key, 86400);
        return true;
    }

}