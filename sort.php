<?php
/**
 * @author     :wkj
 * @createTime :2017/5/2 13:35
 * @description:
 */

$arr = array(
    1,
    43,
    54,
    62,
    21,
    66,
    32,
    78,
    36,
    76,
    39
);

//快速排序
function quick($arr){
    $length = count($arr);
    if ($length <= 1) {
        return $arr;
    }
    $baseNum = $arr[0];
    $leftArr = $rightArr = array();

    foreach ($arr as $key => $vo) {
        if ($key == 0)
            continue;
        if ($vo > $baseNum) {
            $leftArr[] = $vo;
        } else {
            $rightArr[] = $vo;
        }
    }

    $leftArr = quick($leftArr);
    $rightArr = quick($rightArr);

    return array_merge($leftArr, array($baseNum), $rightArr);
}

//冒泡排序
function bubble($arr){
    for ($len = count($arr), $i = 1; $i < $len; $i++) {
        for ($j = 0; $j < $len - $i; $j++) {
            if ($arr[$j] > $arr[$j + 1]) {
                $tmp = $arr[$j + 1];
                $arr[$j + 1] = $arr[$j];
                $arr[$j] = $tmp;
            }
        }

        return $arr;
    }

}

$arr = bubble($arr);
print_r($arr);