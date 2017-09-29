<?php

header('Content-type:text/html; charset=utf-8');
ob_start();
date_default_timezone_set('PRC');
session_start();

function scan($dir){
    $map = [];
    preg_match('/\/$/', $dir) OR $dir .= '/';
    $dir = rtrim($dir, '/');
    if (is_dir($dir) && ($dh = opendir($dir))) {
        while (false !== ($file = readdir($dh))) {
            if (in_array($file, ['.', '..'])) continue;
            $path = "{$dir}/{$file}";
            if (is_dir($path)) {
                $map += scan($path);
            } else {
                if (preg_match('/\.(mp3|wma|wav|m4a|ape|flac|aac)$/', $path)) {
                    $k = sha1($path);
                    $map[$k] = [
                        'path' => $path,
                        'file' => $file,
                    ];
                }
            }
        }
    }
    return $map;
}

$map = scan('C:/Users/Administrator/tmp/wifi-music/sdcard');
file_put_contents('list.json', json_encode($map));
echo json_encode(['map' => $map, 'len' => count($map)]);