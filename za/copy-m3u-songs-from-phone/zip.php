<?php

$zip = new ZipArchive();
$zipFile = "songs_".date('YmdHi');
if (true !== $zip->open($zipFile, ZipArchive::CREATE)) {
    die('zip error!');
}
   
$list = file("Favorites.m3u.txt");
foreach ($list as $v) {
    $f = trim($v);
    if (empty($f)) continue;
    $zip->addFile($f, basename($f));
    $msg = "added {$f} \n";
}
$zip->close();
echo 'done!';