<?php
while (1) {
    $c = file_get_contents("http://huya.cms.duowan.com/cron/UpdateVideoArticleRelate?limit=200&recent=0");
    echo $c;
}