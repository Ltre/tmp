<?php

$month = 201711;
$startDate = $month.'01';
$endDate = date('Ymd', strtotime($month.'01 +1 month -1 day'));
for ($date = $startDate; $date <= $endDate; ) {
    echo $date."\n";
    $date = date('Ymd', strtotime($date . ' +1 day'));//OR $date ++;
}