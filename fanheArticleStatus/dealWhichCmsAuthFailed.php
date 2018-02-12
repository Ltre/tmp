#!/usr/local/php/bin/php
<?php
/**
 * ����msg like "get editor realname failed, udb%"
 * �����·����£��ٱ��ʧ��
 */

include __DIR__.'/config.php';
include __DIR__.'/dwHttp.php';
include __DIR__.'/Model.php';
include __DIR__.'/SeniorModel.php';

$tmpdir = __DIR__.'/tmp';
@mkdir($tmpdir, 0777, true);

//��ʼ�ط�
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
die;//@todo: ���滹û���Ժ�

//��һ��ʱ����ǰ�����Գ������ݣ���Ϊʧ��
$sql = "select l.vid, u.title, l.msg from v_fanhe_article_last_log l, v_fanhe_upload u where l.vid=u.vid and msg like 'get editor realname failed, udb%' and u.status=0 and u.create_time < :time";
$model = new Model('v_fanhe_upload', 'mysql_dev');
$list = $model->query($sql, ['time' => strtotime(date('Ymd'))]) ?: [];//��������ǰ��
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
