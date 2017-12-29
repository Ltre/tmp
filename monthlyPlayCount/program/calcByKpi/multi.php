<?php
@$uniqTaskId = $_SERVER['argv'][1] ?: date('YmdHis');

$tmpDir = $uniqTaskId;
@mkdir($tmpDir, 0777, true);

$nohupFile = 'nohup_tmp.sh';
$nohupShell = [];

$udbList = file('udblist.csv');
@$chunkSize = $_SERVER['argv'][2] ?: 3;//分片长度
$chunks = array_chunk($udbList, $chunkSize);
foreach ($chunks as $k => $v) {
    $chunkDir = "{$tmpDir}/{$k}";
    @mkdir($chunkDir, 0777, true);
    @copy('dwHttp.php', "{$chunkDir}/dwHttp.php");
    @copy('Model.php', "{$chunkDir}/Model.php");
    @copy('config.php', "{$chunkDir}/config.php");
    @copy('kpi.php', "{$chunkDir}/kpi.php");
    file_put_contents("{$chunkDir}/udblist.csv", str_replace(["\r\n", "\r\r", "\n\n"], "\n", join("\n", $v)));
    
    file_put_contents("{$chunkDir}/kpi.sh", "cd {$chunkDir}; /usr/local/php/bin/php kpi.php {$uniqTaskId}");
    $nohupShell[] = "nohup ./{$chunkDir}/kpi.sh >{$chunkDir}/nohup.log &";
}

file_put_contents($nohupFile, join("\n", $nohupShell));