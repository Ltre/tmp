<?php

include_once(__DIR__."/BaseQueue.php");

/**
 * 示例队列 - 前台
 */
class ExampleQueue extends BaseQueue {

    public function __construct(){
        $this->keyPre = $GLOBALS['redis_key']['example']['queue'];
        $this->cliCookieName = 'pgamecli';
        $this->queueNum = 3;
    }

}
