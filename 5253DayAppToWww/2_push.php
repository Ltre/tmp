<?php
include 'lib/dwHttp.php';

function postArticleByBackend($vid, $channel, $title, $content, $tags, $other = array()){
    $data = array(
        'channel' => (string) $channel,
        'title' => (string) $title,
        'content' => (string) $content,
        'tags' => (string) $tags,
        'other' => $other,
        'time' => (string) time(),//使用string，确保双方得出相同的sign
    );
    $token = '9b1b3cdfcf5ec35a631c29ac210c25ef4fc9bd8d';//这里暂时固定为一个token，除非调用方来自多个系统，才考虑按系统分配
    ksort($data, SORT_REGULAR);
    $calcSign = hash_hmac('sha1', json_encode($data), $token);
    $data['sign'] = $calcSign;
    $http = new dwHttp();
    $ret = $http->post("http://61.147.186.105/api/postCmsArticle", $data, 30, "Host: huya.cms.duowan.com");
    $json = json_decode($ret, 1);
    if (false === $ret || ! $ret['rs']) {
        file_put_contents('pushLog', 'VID='.$vid . '      '.print_r(array('vid'=>$vid, 'postData'=>$data, 'ret' =>$ret), 1)."\r\n", FILE_APPEND);
    }
    return $ret;
}

$json = file_get_contents('export') ?: '[]';
$list = json_decode($json, 1);
print_r($list);
die;
foreach ($list as $v) {
    $tags = '2017新闻中心,2016新闻栏目,2017游戏要闻,2017原创视频,每日app,5253';
    $ret = postArticleByBackend(0, 'ceshi', $v['title'], $v['content'], $tags, array(
        // 'digest' => '这是摘要'.microtime(1),
        // 'author' => '多玩原创视频团队',
        // 'source' => '多玩原创',
        // 'coverUrl' => '',
        'digest' => $v['desc'],
        'author' => '多玩原创视频团队',
        'source' => '多玩原创',
        'coverUrl' => $v['cover'],
    ));
    var_dump($ret);
    echo '<br>';
    if (false === $ret) die('net error!');
    $ret = json_decode($ret, 1);
    if (! $ret['rs'] || ! $ret['postRet']['success']) die('push fail');
    $articleId = $ret['postRet']['articleId'];
    print_r($ret);    
}

