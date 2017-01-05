<?php
mb_internal_encoding("GB2312");//���ļ����б��뻷��Ϊ��������

function rmdir_force($dir){
    if (! is_dir($dir)) return;
    $list = scandir($dir);
    foreach ($list as $v) {
        if (in_array($v, array('.', '..'))) continue;
        $curr = "{$dir}/{$v}";
        if ('dir' == filetype($curr)) rmdir_force($curr);
        else unlink($curr);
    }
    reset($list);
    @rmdir($dir);
}

//ѡ���������õ�php�ļ���ͨ����������������php start.php "fencheng-lite.php"
$phpFile = $_SERVER['argv'][1];
if (! in_array($phpFile, array('fencheng-lite.php', 'fencheng.php', 'fencheng-calcmore.php'))) die('argv[1] for $phpFile is error!');

$pieceLen = 20;//ÿ����������UID����
$uidsFile = 'uids.txt';
$uids = file($uidsFile);
$uidPieces = array_chunk($uids, $pieceLen);

//��������Ŀ¼��ִ�в��м���
$cacheDir = 'cache_fencheng_'.date('YmdHis');
$finishFile = 'finish.is';//�����ϱ��������ʱ��д����ļ���
rmdir_force($cacheDir);
@mkdir($cacheDir, 0777, true);
foreach ($uidPieces as $k => $piece) {
    $currPath = "{$cacheDir}/{$k}";
    @mkdir($currPath, 0777, true);
    foreach ($piece as $line) file_put_contents("{$currPath}/{$uidsFile}", $line, FILE_APPEND);
    @copy($phpFile, "{$currPath}/{$phpFile}");
    file_put_contents("{$currPath}/{$phpFile}", "; file_put_contents('{$finishFile}', '1');", FILE_APPEND);//ע����룺��д�ļ���ʽ�ϱ��Ƿ�ִ�����
    file_put_contents("{$currPath}/{$phpFile}.bat", "start \"��ǰ����{$cacheDir}\\{$k}\" php {$phpFile}");
    exec("start /D \"{$cacheDir}\\{$k}\" {$phpFile}.bat");
}

//�ȴ�����������ɺ󣬺ϲ�ִ�н����Ŀǰֻ���upload_uidĿ¼���кϲ���������û��Ҫ��
$waitDur = 0;
$sleepGap = 2;
while (1) {
    $wait = false;
    foreach ($uidPieces as $k => $piece) {
        if (! file_exists("{$cacheDir}/{$k}/{$finishFile}")) $wait = true;
    }
    if ($wait) {
        echo "waiting for the result of calculate, has lasted for {$waitDur} s ...\r\n";
        sleep($sleepGap);
    } else {
        @mkdir("{$cacheDir}/result", 0777, true);
        $collectTotalCsv = array();
        $collectAdTotalCsv = array();
        $varNameMap = array(
            'total.csv' => 'collectTotalCsv', 
            'ad-total.csv' => 'collectAdTotalCsv',
        );
        foreach ($uidPieces as $k => $piece) {
            $dataPath = "{$cacheDir}/{$k}/data/upload_uid";
            $list = scandir($dataPath);
            foreach ($list as $v) {
                if (in_array($v, array('.', '..'))) continue;
                if (in_array($v, array('total.csv', 'ad-total.csv'))) {
                    $tmpCollV = $varNameMap[$v];
                    foreach (file("{$dataPath}/{$v}") as $vv) {
                        if ('' == trim($vv)) continue;
                        if (false !== array_search($vv, $$tmpCollV)) continue;//ȥ���ظ��ı�����
                        $$tmpCollV[] = $vv;//�������ִ�б����ͻ���array_push($$tmpCollV, $vv)
                    }//�ֱ��ռ���ad-total.csv��total.csv������
                } else {
                    @copy("{$dataPath}/{$v}", "{$cacheDir}/result/{$v}");//����ÿ��uid��Ӧ��csv
                }
            }
        }
        foreach ($varNameMap as $kFile => $vVarName) {
            foreach ($$vVarName as $vLine) {
                file_put_contents("{$cacheDir}/result/{$kFile}", $vLine, FILE_APPEND);
            }//д��ϲ����ad-total.csv��total.csv
        }
        echo 'calc finish!';
        break;
    }
    $waitDur += $sleepGap;
}