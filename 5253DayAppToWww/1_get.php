<?php

include 'lib/phpQuery/phpQuery.php';
include 'lib/php-qr-decoder/lib/QrReader.php';

@$p = file_get_contents('p') ?: 56;
//@$p = file_get_contents('p') ?: 1;
file_put_contents('log', '');
file_put_contents('export', '');//用于导出的数据

$p = 5;//test

while ($p >= 1) {
//while ($p <= 56) {
    echo "current page is : {$p} \r\n";
    file_put_contents('log', "current page is : {$p} \r\n", FILE_APPEND);
    
    $url = "http://www.5253.com/zhuanlan/27/list-{$p}-time.html";
    $listContent = file_get_contents($url);
    phpQuery::newDocumentHTML($listContent);
    $objLi = pq('body > ul.show_video_wrap > li');
    $list = array();
    phpQuery::each($objLi, function($i, $e) use (&$list){
        $data = array();
        $objE = pq($e);
        $objA = $objE->find('>a:first');
        $data['url'] = 'http://www.5253.com' . $objA->attr('href');
        $objImg = $objA->find('>img:first');
        $data['cover'] = $objImg->attr('src');
        //$data['publishTime'] = $objE->find('>div.bottom>p.head>span:first')->text() . ' 00:00:00';
        $data['title'] = $objE->find('>div.bottom>p.des>a:first')->text();
        $list[] = $data;
    });
    foreach ($list as $k => $v) {
        $c = file_get_contents($v['url']);
        phpQuery::newDocumentHTML($c);
        $gameName = pq('body > div.wrapper > div.wrapcont > div.details > div > div.conts-article.pd-col.article-cont > div > div > div.info > h5 > a')->text();
        $gameName2 = pq('body > div.wrapper > div.wrapcont > div.details > div > div.conts-article.pd-col.article-cont > div.dwl-temp.frame-temp > strong > div > div.info > h5 > a')->text();
        $gameName3 = pq('body > div.wrapper > div.wrapcont > div.details > div > div.conts-article.pd-col.article-cont > div > strong > strong > div > div.info > h5 > a')->text();
        $gameName4 = pq('body > div.wrapper > div.wrapcont > div.details > div > div.conts-article.pd-col.article-cont > div > div:nth-child(3) > div > div.info > h5 > a')->text();
        $gameName5 = pq('body > div.wrapper > div.wrapcont > div.details > div > div.conts-article.pd-col.article-cont > div > p:nth-child(1) > span:nth-child(2) > a:nth-child(2)')->text();
        preg_match('/《(.+)》/', pq('body > div.wrapper > div.wrapcont > div.details > div > div.details-top > div.game-introdu > div > h5')->text(), $matches);
        @$gameName6 = $matches[1] ?: '';
        preg_match('/每日APP第\d+期[：\:](.+)/i', pq('head > title:first')->text(), $matches);
        @$gameName7 = $matches[1] ?: '';
        $desc = pq('body > div.wrapper > div.wrapcont > div.details > div > div.conts-article.pd-col.article-cont > p:nth-child(1)')->text();
        $desc2 = pq('body > div.wrapper > div.wrapcont > div.details > div > div.conts-article.pd-col.article-cont > p:nth-child(2)')->text();//别的版本的介绍
        $desc3 = pq('body > div.wrapper > div.wrapcont > div.details > div > div.conts-article.pd-col.article-cont > p:nth-child(3)')->text()
               . pq('body > div.wrapper > div.wrapcont > div.details > div > div.conts-article.pd-col.article-cont > p:nth-child(4)')->text();
        $desc4 = pq('body > div.wrapper > div.wrapcont > div.details > div > div.conts-article.pd-col.article-cont > div:nth-child(3)')->text();
        $videoTpl = pq('body > div.wrapper > div.wrapcont > div.details > div > div.conts-article.pd-col.article-cont > p:nth-child(2)')->html();
        if (! preg_match('/video\/vp+\.swf\?/', $videoTpl) || ! preg_match('/application\/x\-shockwave\-flash/i', $videoTpl) || $videoTpl == '<strong>游戏简介：</strong>' || preg_match('/5253官方暴虐粉丝群/', $videoTpl)) {
            $videoTpl = pq('body > div.wrapper > div.wrapcont > div.details > div > div.conts-article.pd-col.article-cont > p:nth-child(5)')->html();
        }
        if (! preg_match('/video\/vp+\.swf\?/', $videoTpl) || ! preg_match('/application\/x\-shockwave\-flash/i', $videoTpl) || $videoTpl == '<strong>游戏简介：</strong>' || preg_match('/5253官方暴虐粉丝群/', $videoTpl)) {
            $videoTpl = pq('body > div.wrapper > div.wrapcont > div.details > div > div.conts-article.pd-col.article-cont > p:nth-child(3)')->html();
        }
        if (! preg_match('/video\/vp+\.swf\?/', $videoTpl) || ! preg_match('/application\/x\-shockwave\-flash/i', $videoTpl) || $videoTpl == '<strong>游戏简介：</strong>' || preg_match('/5253官方暴虐粉丝群/', $videoTpl)) {
            $videoTpl = pq('body > div.wrapper > div.wrapcont > div.details > div > div.conts-article.pd-col.article-cont > p:nth-child(3)')->html();
        }
        if (! preg_match('/video\/vp+\.swf\?/', $videoTpl) || ! preg_match('/application\/x\-shockwave\-flash/i', $videoTpl) || $videoTpl == '<strong>游戏简介：</strong>' || preg_match('/5253官方暴虐粉丝群/', $videoTpl)) {
            $videoTpl = pq('body > div.wrapper > div.wrapcont > div.details > div > div.conts-article.pd-col.article-cont > p:nth-child(7)')->html();
        }
        if (! preg_match('/video\/vp+\.swf\?/', $videoTpl) || ! preg_match('/application\/x\-shockwave\-flash/i', $videoTpl) || $videoTpl == '<strong>游戏简介：</strong>' || preg_match('/5253官方暴虐粉丝群/', $videoTpl)) {
            $videoTpl = pq('body > div.wrapper > div.wrapcont > div.details > div > div.conts-article.pd-col.article-cont > p:nth-child(5)')->text();
        }
        if (! preg_match('/video\/vp+\.swf\?/', $videoTpl) || ! preg_match('/application\/x\-shockwave\-flash/i', $videoTpl) || $videoTpl == '<strong>游戏简介：</strong>' || preg_match('/5253官方暴虐粉丝群/', $videoTpl)) {
            $videoTpl = pq('body > div.wrapper > div.wrapcont > div.details > div > div.conts-article.pd-col.article-cont > p:nth-child(8)')->text();
        }
        if (! preg_match('/video\/vp+\.swf\?/', $videoTpl) || ! preg_match('/application\/x\-shockwave\-flash/i', $videoTpl) || $videoTpl == '<strong>游戏简介：</strong>' || preg_match('/5253官方暴虐粉丝群/', $videoTpl)) {
            $videoTpl = pq('body > div.wrapper > div.wrapcont > div.details > div > div.conts-article.pd-col.article-cont > p:nth-child(6)')->text();
        }
        if (! preg_match('/video\/vp+\.swf\?/', $videoTpl) || ! preg_match('/application\/x\-shockwave\-flash/i', $videoTpl) || $videoTpl == '<strong>游戏简介：</strong>' || preg_match('/5253官方暴虐粉丝群/', $videoTpl)) {
            preg_match('/\<embed\s+type\=[\'"]\s*application\/x\-shockwave\-flash\s*[\'"].*\/video\/vp+\.swf.*\/\>/', $c, $matches);
            @$videoTpl = $matches[0];
        }
        if (! preg_match('/video\/vp+\.swf\?/', $videoTpl) || ! preg_match('/application\/x\-shockwave\-flash/i', $videoTpl) || $videoTpl == '<strong>游戏简介：</strong>' || preg_match('/5253官方暴虐粉丝群/', $videoTpl)) {
            preg_match('/\<embed\s+.*\/video\/vp+\.swf.*\/\>/', $c, $matches);
            @$videoTpl = $matches[0];
        }
        if (empty($videoTpl)) {
            echo 'fuckyou!! url is ' . $v['url'] . "\r\n";
        }
        /* if (! preg_match('/video\/vp+\.swf\?/', $videoTpl) || ! preg_match('/application\/x\-shockwave\-flash/i', $videoTpl) || $videoTpl == '<strong>游戏简介：</strong>' || preg_match('/5253官方暴虐粉丝群/', $videoTpl)) {
            $videoTpl .= 'QQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQ';
        } */
        $dl = pq('body > div.wrapper > div.wrapcont > div.details > div > div.conts-article.pd-col.article-cont > div > div > a.btn')->attr('href');
        $dl2 = str_replace('http://www.5253.com', '', pq('body > div.wrapper > div.wrapcont > div.details > div > div.conts-article.pd-col.article-cont > div > p:nth-child(1) > span > a')->attr('href'));
        $dl3 = pq('body > div.wrapper > div.wrapcont > div.details > div > div.conts-article.pd-col.article-cont > div > strong > div > a.btn')->attr('href');
        $dl4 = pq('body > div.wrapper > div.wrapcont > div.details > div > div.conts-article.pd-col.article-cont > div > strong > strong > div > div.info > h5 > a')->attr('href');
        $dl5 = pq('body > div.wrapper > div.wrapcont > div.details > div > div.conts-article.pd-col.article-cont > div > p:nth-child(1) > a')->attr('href');
        $dl6 = pq('body > div.wrapper > div.wrapcont > div.details > div > div.conts-article.pd-col.article-cont > div > div > strong > div > a.btn')->attr('href');
        $dl7 = pq('body > div.wrapper > div.wrapcont > div.details > div > div.conts-article.pd-col.article-cont > div > div.right.fr > a.android')->attr('href');
        $dl8 = pq('body > div.wrapper > div.wrapcont > div.details > div > div.conts-article.pd-col.article-cont > div:nth-child(8) > strong > div > div > a.btn')->attr('href');
        $dl9 = pq('body > div.wrapper > div.wrapcont > div.details > div > div.conts-article.pd-col.article-cont > div > div > div > a.btn')->attr('href');
        $dl10 = pq('body > div.wrapper > div.wrapcont > div.details > div > div.conts-article.pd-col.article-cont > div.info-game-dwl.clear > div > div.igd-dwl-box > a')->attr('href');
        $dl11 = pq('body > div.wrapper > div.wrapcont > div.details > div > div.conts-article.pd-col.article-cont > div > strong > div.dwl-temp.frame-temp > div > a.btn')->attr('href');
        $dl12 = pq('body > div.wrapper > div.wrapcont > div.details > div > div.conts-article.pd-col.article-cont > div.noMobileDiv > strong > div.dwl-temp.frame-temp > div > a.btn')->attr('href');
        $dl99 = '';
        if (empty($dl)) {
            $imgSrc = pq('body > div.wrapper > div.wrapcont > div.details > div > div.conts-article.pd-col.article-cont > div.noMobileDiv > div > p:nth-child(6) > img')->attr('src');
            if (empty($imgSrc)) $imgSrc = pq('body > div.wrapper > div.wrapcont > div.details > div > div.conts-article.pd-col.article-cont > div > p:nth-child(2) > img')->attr('src');
            if (empty($imgSrc)) $imgSrc = pq('body > div.wrapper > div.wrapcont > div.details > div > div.conts-article.pd-col.article-cont > p:nth-child(11) > img')->attr('src');
            if (empty($imgSrc)) $imgSrc = pq('body > div.wrapper > div.wrapcont > div.details > div > div.conts-article.pd-col.article-cont > div.noMobileDiv > div > p:nth-child(7) > img')->attr('src');
            if (empty($imgSrc)) $imgSrc = pq('body > div.wrapper > div.wrapcont > div.details > div > div.conts-article.pd-col.article-cont > div > div > p:nth-child(7) > img')->attr('src');
            if (empty($imgSrc)) $imgSrc = pq('body > div.wrapper > div.wrapcont > div.details > div > div.conts-article.pd-col.article-cont > div > strong > div:nth-child(2) > p:nth-child(6) > img')->attr('src');
            if (empty($imgSrc)) $imgSrc = pq('body > div.wrapper > div.wrapcont > div.details > div > div.conts-article.pd-col.article-cont > div > strong > div > p:nth-child(6) > img')->attr('src');
            if (empty($imgSrc)) $imgSrc = pq('body > div.wrapper > div.wrapcont > div.details > div > div.conts-article.pd-col.article-cont > div.noMobileDiv > strong > div > p:nth-child(7) > img')->attr('src');
            if (empty($imgSrc)) $imgSrc = pq('body > div.wrapper > div.wrapcont > div.details > div > div.conts-article.pd-col.article-cont > div > strong > div > p:nth-child(7) > span > strong > img')->attr('src');
            if (empty($imgSrc)) $imgSrc = pq('body > div.wrapper > div.wrapcont > div.details > div > div.conts-article.pd-col.article-cont > div > strong > div > p:nth-child(7) > strong > img')->attr('src');
            if ($imgSrc) {                
                $qrcode = new QrReader($imgSrc);
                $dl99 = $qrcode->text(); //识别下载二维码
            }
        }
        $list[$k]['dl'] = $dl = 'http://sy.duowan.com' .($dl ?: $dl2 ?: $dl3 ?: $dl4 ?: $dl5 ?: $dl6 ?: $dl7 ?: $dl9 ?: $dl9 ?: $dl10 ?: $dl11 ?: $dl99 ?: '');
        $list[$k]['gameName'] = $gameName = $gameName ?: $gameName2 ?: $gameName3 ?: $gameName4 ?: $gameName5 ?: $gameName6 ?: $gameName7 ?: '传送门';
        $list[$k]['videoTpl'] = $videoTpl;
        $list[$k]['desc'] = $desc = $desc ?: $desc2 ?: $desc3 ?: $desc4;
        $list[$k]['content'] = "<p>{$desc}</p><p>游戏下载：<a href=\"{$dl}\">{$gameName}</a></p><p style=\"text-align: center;\">{$videoTpl}</p>";
        //按行导出数据
        file_put_contents('export', json_encode($list[$k])."\r\n", FILE_APPEND);
        echo "In the foreach " . str_repeat("#", $k+1) . "\r\n";
    }
    file_put_contents('log', print_r($list, 1), FILE_APPEND);

    $p --;
    //$p ++;
    file_put_contents('p', $p);

    /* sleep(1);
    echo "waiting.. 5\r\n";
    sleep(1);
    echo "waiting.. 4\r\n";
    sleep(1);
    echo "waiting.. 3\r\n";
    sleep(1);
    echo "waiting.. 2\r\n";
    sleep(1);
    echo "waiting.. 1\r\n"; */
}
