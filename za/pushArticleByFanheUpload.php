<?php

while (true) {
    $url = 'http://huya.cms.duowan.com/cron/pushArticleByFanheUpload';
    echo file_get_contents($url);
    sleep(1);
}