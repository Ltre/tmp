<?php

define('AFTP_HOST', '172.16.15.244');
define('AFTP_PORT', '8000');
define('AFTP_USER', 'qqqq');
define('AFTP_PSWD', 'qqqq');


function connect(){
    $conn = ftp_connect(AFTP_HOST, AFTP_PORT);
    if (! $conn) {
        return [false, 'connection error!', null];
    }
    if (! @ftp_login($conn, AFTP_USER, AFTP_PSWD)) {
        return [false, 'login failure!', null];
    }
    return [true, 'ok', $conn];
}


function download($conn, $remoteFile, $localFile){
    $msg = "{$remoteFile} -> {$localFile}";
    if (ftp_get($conn, $localFile, $remoteFile, FTP_BINARY)) {
        return [true, "Download ok, {$msg}\n"];
    } else {
        return [false, "Download error, {$msg}\n"];
    }
}


function main(){
    list ($succ, $msg, $conn) = connect();
    if (! $succ) {
        die($msg);
    }
    
    $list = file("AlarmClock.m3u");
    $songsDir = 'songs_'.date('YmdHi');
    @mkdir($songsDir);
    foreach ($list as $v) {
        $f = trim($v);
        if (empty($f)) continue;
        list ($succ, $msg) = download($conn, $f, $songsDir.'/'.basename($f));
        echo $msg;
    }
    
    echo "Done!";
}

main();