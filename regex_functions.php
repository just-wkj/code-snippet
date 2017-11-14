<?php
//几个正则函数比较好用的


//preg_replace_callback(string|array,callback,subject,limit,num);
//匹配到的执行函数进行替换
$text = "April fools day 12334 is 04/01/2002  10/11/2017\n";
$text .= "Last christmas was 12/24/2001\n";
echo preg_replace_callback(array("|(\d{2}/\d{2}/)(\d{4})|"), function ($out){
    return $out[1] . ($out[2] + 1);
}, $text, -1, $num);
/*
April fools day 12334 is 04/01/2003  10/11/2018
Last christmas was 12/24/2001
2
*/

//preg_split(str,subject,limit,type) 类似explode函数比较强大
$str = ",,,::php:::::mysql,apache ajax ";
$keywords = preg_split("/[:\s,]+/", $str,-1,true);
/*
 * Array
(
    [0] => php
    [1] => mysql
    [2] => apache
    [3] => ajax
)
 * */


