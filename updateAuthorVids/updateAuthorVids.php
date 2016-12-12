<?php
$url = 'http://huya.cms.duowan.com/cron/updateAuthorVids';
$channel = @$_SERVER['argv'][1] ?: '';
if (! empty($channel)) $url .= "?channel={$channel}";
echo "<br>{$url}<br>";

while(1) {
    echo file_get_contents($url);
}
