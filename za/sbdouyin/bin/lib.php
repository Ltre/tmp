<?php

function curlGet($url, $header="", $ssl_verify = false,  $timeout=20) {
    $header = empty($header) ? $this->defaultHeader() : $header;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    curl_setopt($ch, CURLOPT_HTTPHEADER, explode("\r\n", $header));//模拟的header头
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $ssl_verify);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $ssl_verify);
    $result = curl_exec($ch);
    
    $info = curl_getinfo($ch);
    $info['url'] = $url;
    
    curl_close($ch);
    return $result;
}


function curlPost($url, $data = [], $header = '', $ssl_verify = false, $timeout = 5){
    $h = "User-Agent:Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9.2.12) Gecko/20101026 Firefox/3.6.12\r\n";
    $h .= "Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n";
    $h .= "Accept-language: zh-cn,zh;q=0.5\r\n";
    $h .= "Accept-Charset: GB2312,utf-8;q=0.7,*;q=0.7\r\n";
    $h .= "Content-type: application/x-www-form-urlencoded; charset=UTF-8\r\n";
    $header = $header ?: $h;
    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_NOSIGNAL=>true,
        CURLOPT_CONNECTTIMEOUT_MS => 200,
        CURLOPT_TIMEOUT_MS => 2000,
        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
        CURLOPT_SSL_VERIFYPEER => $ssl_verify,
        CURLOPT_SSL_VERIFYHOST => $ssl_verify,
        CURLOPT_HTTPHEADER => explode("\r\n", $header),
        CURLOPT_POSTFIELDS => http_build_query($data),
    ));
    $ret = curl_exec($ch);
    curl_close($ch);
    return $ret;
}