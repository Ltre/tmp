#!/usr/local/php/bin/php
<?php

include __DIR__.'/config.php';
include __DIR__.'/Model.php';

include __DIR__.'/3-fix.php';

@$t = $_SERVER['argv'][1] ?: 0;

$model = new Model('yyuid_to_openid_'.$t, 'mysql');
$fix = new Fix();
$fix->fixData($model, $t);

// for ($i = 0; $i < 10; $i ++) {    
// }