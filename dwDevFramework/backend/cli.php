#!/usr/local/php/bin/php
<?php
/**
 * 简单粗暴
 *      ./cli.php test/hehe
 *      ./cli.php "test/hehe?a=1&b=2&c=%E5%91%B5%E5%91%B5"
 *      ./cli.php "http://abc.dom/test/hehe?a=1&b=2&c=%E5%91%B5%E5%91%B5"
 * 访问controller/action
 *      ./cli.php -c test -a hehe #Go to TestController::actionHehe()
 *      ./cli.php -ctest -ahehe #Go to TestController::actionHehe()
 * 使用post/get参数
 *      --post="a=1&b=fdsfa&c=%E5%91%B5%E5%91%B5"
 *      -p "a=1&b=fdsfa&c=%E5%91%B5%E5%91%B5" #POST简写
 *      --get="a=1&b=fdsfa&c=%E5%91%B5%E5%91%B5"
 *      -g "a=1&b=fdsfa&c=%E5%91%B5%E5%91%B5" #GET简写
 */

function __nobody_use_this_name(){
    $opts = getopt('c:a:p:g:', ['post:', 'get:', 'dev']);
    if (empty($opts)) {
        $a = $_SERVER['argv'];
        if (! isset($a[1])) die('no params!');
        $simpleArg = $a[1];
        if (preg_match('/^\/?\w+\/\w+$/', $simpleArg)) {
            $_REQUEST['r'] = $simpleArg;
            $_SERVER['REQUEST_URI'] = '/'.ltrim($simpleArg, '/');
        } else {
            $isUrl = preg_match('/^(https?\:\/\/)?[\w_\-\/\.]+\.[\w_\+\-\/\.\,:;&#@=~%\?]+/', $simpleArg);
            $likeUri = preg_match('/^\/?\w+\/\w+\?.*?/', $simpleArg);
            if ($likeUri) {
                $simpleArg = 'http://abc.com/'.ltrim($simpleArg, '/');
            }
            $parse = parse_url($simpleArg);
            @parse_str($parse['query'], $get);
            $_GET = $get;
            if (isset($_GET['r'])) {
                $_REQUEST['r'] = $_GET['r'];
            } else {
                $_REQUEST['r'] = ltrim($parse['path'], '/');
            }
            $_SERVER['REQUEST_URI'] = '/'.ltrim($parse['path'], '/');
        }
    } else {
        @$controller = $opts['c'] ?: 'default';
        @$action = $opts['a'] ?: 'index';
        $_REQUEST['r'] = "{$controller}/{$action}";

        @parse_str($opts['p'] ?: $opts['post'] ?: '', $post);
        @parse_str($opts['g'] ?: $opts['get'] ?: '', $get);
        $_POST = $post;
        $_GET = $get;
        $_GET['r'] = $_REQUEST['r'];//特殊处理r参数
        $_SERVER['REQUEST_URI'] = "/{$_GET['r']}";
    }
}

__nobody_use_this_name();

require_once __DIR__.'/index.php';
