#!/usr/local/php/bin/php
<?php
//放置到221.228.91.194运行，完毕后会生成afterPush.js文件，仔细根据js文件里的提示去做

require_once __DIR__.'/config.php';
require_once __DIR__.'/Model.php';
require_once __DIR__.'/SeniorModel.php';
require_once __DIR__.'/dwHttp.php';
require_once __DIR__.'/dwFile.php';
require_once __DIR__.'/dwImagick.php';
require_once __DIR__.'/Iuc.php';

class Bot {
    
    private $logFile;
    private $limit = 20;
    private $p = 1;
    private $startP = 1;
    private $data4AfterPush = [];
    
    private $channelMap = [
        53842 => 'smsy', //蜀门手游smsy.duowan.com
        /* 53650 => 'dnsy', //龙之谷手游dnsy.duowan.com
        50014 => 'qqhcs', //青丘狐传说qqhcs.duowan..com
        49882 => 'mhzxsy', //梦幻诛仙手游mhzxsy.duowan.com
        46981 => 'sbk', //沙巴克传奇sbk.duowan.com
        46972 => 'jx3sy', //剑侠情缘移动版jx3sy.duowan.com
        46600 => 'gjsy', //古剑奇谭壹之莫忘初心gjsy.duowan.com
        45976 => 'wd', //问道wd.duowan.com
        43765 => 'm3sy', //梦三国手游m3sy.duowan.com
        42184 => 'wlwzsy', //武林外传wlwzsy.duowan.com
        30760 => 'xjsy', //新仙剑奇侠传xjsy.duowan.com
        5332 => 'musy', //musy.duowan.com */
    ];
    
    private $templateIdMap = [
        53842 => '381436992865', //蜀门手游smsy.duowan.com
        /* 53650 => 'dnsy', //龙之谷手游dnsy.duowan.com
        50014 => 'qqhcs', //青丘狐传说qqhcs.duowan..com
        49882 => 'mhzxsy', //梦幻诛仙手游mhzxsy.duowan.com
        46981 => 'sbk', //沙巴克传奇sbk.duowan.com
        46972 => 'jx3sy', //剑侠情缘移动版jx3sy.duowan.com
        46600 => 'gjsy', //古剑奇谭壹之莫忘初心gjsy.duowan.com
        45976 => 'wd', //问道wd.duowan.com
        43765 => 'm3sy', //梦三国手游m3sy.duowan.com
        42184 => 'wlwzsy', //武林外传wlwzsy.duowan.com
        30760 => 'xjsy', //新仙剑奇侠传xjsy.duowan.com
        5332 => 'musy', //musy.duowan.com */
    ];
    
    function __construct(){
        $this->logFile = __DIR__.'/log.txt';
        file_put_contents($this->logFile, '');
    }
    
    function start(& $isLastP = false){
        $this->setCurrP($this->startP);
        $gameIds = array_keys($this->channelMap);
        $model = new SeniorModel('tb_article', 'mysql');
        $selectData = $model->seniorSelect([
            'where' => ['gameId', 'IN', $gameIds],
            'limitBy' => [$this->p, $this->limit, 10],
            'listable' => true,
            'pageable' => true,
        ]);
        $list = $selectData['list'];
        $pages = $selectData['pages'];
        $this->log("list count: ".count($list).", page: ".$p.", total_page: ".$pages['total_page']);
        $this->log("===================Show list below: =====================");
        foreach ($list as $v) {
            $this->doit($v);
        }
        $isLastP = $pages['total_page'] <= $p;
        $this->setNextP($isLastP);
    }
    
    
    function doit($v){
        $v['tags'] = $this->getTags($v);
        $v['channel'] = $this->channelMap[$v['gameId']];
        $v['templateId'] = $this->templateIdMap[$v['gameId']];
        $cover = $this->upByImgUrl('http://image.5253.com/'.ltrim($v['coverImage'], '/'));
        $v['cover'] = $cover;//备用：$v['images']有多个地址，用逗号隔开；$v['coverImage']仅有一个地址
        $v['content'] = preg_replace('/{insertgame\:\d+\:\d+}/', '', $v['content']);
        //$this->log("v is: ".json_encode($v, JSON_UNESCAPED_UNICODE));
        $this->log("v is: ".var_export($v, 1));
        $this->pushArticle($v);
    }

    
    private function setCurrP($startP){
        @$p = file_get_contents(__DIR__.'/p') ?: false;
        if (false === $p) {
            $p = $startP;
            file_put_contents(__DIR__.'/p', $p);
        }
        $this->p = $p;
    }


    private function setNextP($isLastP = false){
        if (! $isLastP) {
            ++ $this->p;
        } else {
            $this->p = 1;
        }
        file_put_contents(__DIR__.'/p', $this->p);
    }
    
    
    function getTags($v){
        $result = [];
        $model = new SeniorModel('tb_game_tag', 'mysql');
        $gameTaglist = $model->select(['gameId' => $v['gameId']], 'tagName') ?: [];
        foreach ($gameTaglist as $v) {
            in_array($v['tagName'], $result) OR array_push($result, $v['tagName']);
        }
        $model = new SeniorModel('tb_article_tag_art', 'mysql');
        $artTaglist = $model->select(['articleId' => $v['articleId']], 'tagArtName') ?: [];
        foreach ($artTaglist as $v) {
            in_array($v['tagArtName'], $result) OR array_push($result, $v['tagArtName']);
        }
        if (empty($result)) $result[] = '默认标签';
        return join(',', $result);
    }


    function pushArticle($v){
        $url = 'http://61.147.186.105/api/postCmsArticle';
        $params = [
            'channel' => (string) $v['channel'],//根据gameId
            'title' => (string) $v['title'],
            'content' => (string) $v['content'],
            'tags' => (string) $v['tags'],//根据tb_game_tag，tb_article_tag_art
            'other' => [
                'digest' => (string) $v['introduction'],
                'author' => (string) $v['authorName'],
                'coverUrl' => (string) $v['cover'],
                'source' => (string) $v['reprintSrc'],
                'userId' => '5253bot',
                'needComment' => '1',
                'publishTime' => $v['createTime'],
                'templateId' => (string) $v['templateId'],
            ],
            'time' => (string) time(),
        ];
        $tokenFile = '/home/fangkunbiao/5253refucktoken';
        if (! file_exists($tokenFile)) throw new Exception("token file not found!");
        @$token = file_get_contents($tokenFile) ?: '';//token到huyaVideoAdmin项目找
        ksort($params, SORT_REGULAR);
        $calcSign = hash_hmac('sha1', json_encode($params), $token);
        $params['sign'] = $calcSign;
        //$this->log("Push params: ".json_encode($params, JSON_UNESCAPED_UNICODE));
        $this->log("Push params: ".var_export($params, 1));
        $ret = (new dwHttp)->post($url, $params, 20, "Host: huya.cms.duowan.com");
        $this->log("Push ret: ".$ret);
        echo "发文章：{$v['title']}\n";
        echo "结果：".$ret."\n";
        //准备后续的事：改文章时间（因推送接口指定发布时间失败）
        $this->afterPush($v, $ret);
    }
    
    
    function afterPush($v, $ret){
        $ret = json_decode($ret?:'[]', 1);
        if (! isset($ret['postRet']['articleId'])) {
            return false;
        }
        $this->data4AfterPush['list'][] = [
            "artiUrl" => "http://cms.duowan.com/article/toEditArticlePage.do?articleId={$ret['postRet']['articleId']}&channelId={$v['channel']}",
            "time" => $v['createTime'],
            "channel" => $v['channel'],
            "articleId" => $ret['postRet']['articleId'],
        ];
        $listCode = json_encode($this->data4AfterPush['list'], JSON_UNESCAPED_UNICODE);
        $js = "
            //在cms.duowan.com域名下执行：
            //console找到名为contentIframe的frame，加上ID abcdefghijklmn
            var list = {$listCode};
            function doit(o, cb){
                document.getElementById('abcdefghijklmn').setAttribute('src', o.artiUrl);
                setTimeout(function(){
                    var win = window['abcdefghijklmn'].contentWindow;
                    var doc = window['abcdefghijklmn'].contentDocument;
                    doc.getElementById('newPublishTimeStr').value = o.time;
                    win.updatePublishTime();
                    setTimeout(function(){
                        cb();
                    }, 2000);
                }, 2000);
            }
            function cb(){
                if (++i < list.length) {
                    doit(list[i], cb);
                }
            }
            i = 0;
            doit(list[i], cb);
        ";
        file_put_contents(__DIR__.'/afterPush.js', $js);
    }
    
    
    //上传封面，返回url
    function upByImgUrl($url, $option = []){
        $client = new ImgUploadClient;
        if (! preg_match('/^https?\:\/\//', $url) && preg_match('/^\/\/\w/', $url)) {
            $url = 'http:' . $url;
        }
        $ret = $client->upByImgUrl($url, $option);
        if ($ret['code'] != 0) return false;
        return $ret['url'];
    }
    
    
    function log($content){
        file_put_contents($this->logFile, $content."\n", FILE_APPEND);
    }
    
}

$bot = new Bot;
while (1) {
    $isLastP = $bot->start();
    if ($isLastP) {
        echo "finish! \n";
        break;
    }
    echo "wait.. \n";
    sleep(1);
}