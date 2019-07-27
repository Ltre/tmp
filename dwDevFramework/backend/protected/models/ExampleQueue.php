<?php

include_once(__DIR__."/BaseQueue.php");

/**
 * 示例队列 - 后台消费
 */
class Login4RolesQueue extends BaseQueue {

    public function __construct(){
        $this->keyPre = $GLOBALS['redis_key']['example']['queue'];
        $this->cliCookieName = 'pgamecli';
        $this->queueNum = 3;
    }


    protected function handleOne($elem){
        $host = DEBUG ? 'a.com' : 'b.com';
        obj('dwHttp')->post("https://{$host}/cron/syncRoleRelate", [
            'uid' => $elem['data']['uid'],
            'gid' => $elem['data']['gid'],
            'sid' => $elem['data']['sid'],
        ]);
        echo " \n";
    }

}
