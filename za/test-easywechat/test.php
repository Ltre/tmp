<?php

use EasyWeChat\Factory;

$options = [
    'app_id'    => 'wx3cf0f39249eb0exxx',
    'secret'    => 'f1c242f4f28f735d4687abb469072xxx',
    'token'     => 'easywechat',
    'log' => [
        'level' => 'debug',
        'file'  => '/tmp/easywechat.log',
    ],
    // ...
];

$app = Factory::officialAccount($options);

$server = $app->server;
$user = $app->user;

$server->push(function($message) use ($user) {
    $fromUser = $user->get($message['FromUserName']);

    return "{$fromUser->nickname} 您好！欢迎关注 overtrue!";
});

$server->serve()->send();