#!/usr/local/php/bin/php
<?php
/**
 * 查找msg like "get editor realname failed, udb%"
 * 先重新发文章，再标记失败
 */

include __DIR__.'/config.php';
include __DIR__.'/dwHttp.php';
include __DIR__.'/Model.php';
include __DIR__.'/SeniorModel.php';

$tmpdir = __DIR__.'/tmp';
@mkdir($tmpdir, 0777, true);

//开始重发
$sql = "select l.vid, u.title, l.msg from v_fanhe_article_last_log l, v_fanhe_upload u where l.vid=u.vid and msg like 'get editor realname failed, udb%' and u.status=0 limit 100";
$model = new Model('v_fanhe_upload', 'mysql');
$list = $model->query($sql) ?: [];
print_r($list);

foreach ($list as $k => $v) {
    $vid = $v['vid'];
    $ret = (new dwHttp)->get("http://61.147.186.105/?r=cron/pushArticleByFanheUpload&vid={$vid}", 20, "Host: huya.cms.duowan.com");
    $output = "vid = {$vid}, redo: {$ret} \n";
    file_put_contents("{$tmpdir}/{$vid}.redo", $output);
    echo $output;
}
for ($i = 0; $i < count($list); ++ $i) {
    $c = file_get_contents("{$tmpdir}/{$vid}.redo");
    file_put_contents("{$tmpdir}/merge.redo", $c, FILE_APPEND);
}

//DEBUG
die;//@todo: 后面还没测试好

//将一定时刻以前的重试出错数据，标为失败
$sql = "select l.vid, u.title, l.msg from v_fanhe_article_last_log l, v_fanhe_upload u where l.vid=u.vid and msg like 'get editor realname failed, udb%' and u.status=0 and u.create_time < :time";
$model = new Model('v_fanhe_upload', 'mysql_dev');
$list = $model->query($sql, ['time' => strtotime(date('Ymd'))]) ?: [];//处理当天以前的
print_r($list);
 
foreach ($list as $k => $v) {
    $vid = $v['vid'];
    $success = false !== $model->update(['vid' => $vid], ['status' => 3]);
    $output = "vid = {$vid}, togged result: ".($success?'success':'fail')." \n";
    file_put_contents("{$tmpdir}/{$vid}.tagged", $output);
    echo $output;
}

for ($i = 0; $i < count($list); ++ $i) {
    $c = file_get_contents("{$tmpdir}/{$vid}.tagged");
    file_put_contents("{$tmpdir}/merge.tagged", $c, FILE_APPEND);
}
