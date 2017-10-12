<?php

//计算某天的+n工作日的日期

function getWorkDay($day, $time = null){
    if ($time === null) {
        $time = time();
    }

    $mark = $day > 0 ? 1 : -1;
    for ($i = abs($day); $i > 0; $i--) {
        $time = $time + $mark * 86400;
        $w = date('w', $time);
        if ($w == 0 || $w == 6) {
            $time = $time + $mark * 86400 * ($mark > 0 ? ($w ? 2 : 1) : ($w ? 1 : 2));
        }
    }

    return date('Y-m-d', $time);
}


$day = -1;
$time = '2017-10-10';
echo getWorkDay($day,strtotime($time));
