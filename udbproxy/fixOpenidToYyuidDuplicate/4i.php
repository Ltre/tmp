#!/usr/local/php/bin/php
<?php
/**
 * 根据o2y，修正openid_to_yyuid_init_*.yyuid
 */

include __DIR__.'/config.php';
include __DIR__.'/Model.php';

include __DIR__.'/4-fix.php';

@$t = $_SERVER['argv'][1] ?: 0;

$fix = new Fix('mysql');
$fix->fixData($t);
