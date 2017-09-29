<?php

ob_start();
date_default_timezone_set('PRC');
session_start();

$k = @$_GET['k'];
if (empty($k)) {
    echo json_encode([
        'rs' => false,
        'data' => null,
        'msg' => 'param err!',
    ]);
    die;
}

$json = @file_get_contents('list.json') ?: '{}';
$map = json_decode($json, 1) ?: [];
if (! isset($map[$k])) {
    echo json_encode([
        'rs' => false,
        'data' => null,
        'msg' => 'not found!',
    ]);
    die;
}

$filePath = $map[$k]['path'];
header('Content-type:'.mime_content_type($filePath));
echo readfile($filePath);
