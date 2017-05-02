<?php
/**
 * @author     :wkj
 * @createTime :2017/5/2 13:16
 * @description:    函数列表
 */


if (!function_exists('curlGet')) {
    /**
     * curl get
     * @author:wkj
     * @date  2017/5/2 13:18
     * @param     $url
     * @param int $timeOut
     * @return bool|mixed
     */
    function curlGet($url, $timeOut = 10){
        $oCurl = curl_init();
        if (stripos($url, "https://") !== false) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1);
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_TIMEOUT, $timeOut);
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if (intval($aStatus["http_code"]) == 200) {
            return $sContent;
        } else {
            return false;
        }
    }
}
/**
 * curl post 方法
 * @param string $url     to request
 * @param array  $post    values to send
 * @param array  $options for cURL
 * @return string
 */
function curlPost($url, array $post = null, array $options = array()){
    $defaults = array(
        CURLOPT_POST           => 1,
        CURLOPT_HEADER         => 0,
        CURLOPT_URL            => $url,
        CURLOPT_FRESH_CONNECT  => 1,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_FORBID_REUSE   => 1,
        CURLOPT_TIMEOUT        => 4,
        CURLOPT_POSTFIELDS     => http_build_query($post)
    );

    $ch = curl_init();
    curl_setopt_array($ch, ($options + $defaults));
    if (!$result = curl_exec($ch)) {
        trigger_error(curl_error($ch));
    }
    curl_close($ch);

    return $result;
}

if (!function_exists('p')) {
    /**
     * 打印函数
     * @author:wkj
     * @date
     * @param string $data
     * @param int    $is_die
     */
    function p($data = 'godie', $is_die = 1){
        $data === 'godie' && die();
        echo "<pre>";
        if (is_bool($data)) {
            var_dump($data);
        } else {
            print_r($data);
        }
        echo "</pre>";
        $is_die && die();
    }
}

/**
 * 数组键大小写转换
 * @param array $data
 * @param int   $case
 * @return array
 * @author wkj
 */
function changeArrayKeyCase(array $data, $case = CASE_LOWER){
    $new = array();
    foreach ($data as $key => $item) {
        if ($case == CASE_UPPER) {
            $keyTemp = strtoupper($key);
        } else {
            $keyTemp = strtolower($key);
        }

        if (is_array($item)) {
            $new[$keyTemp] = changeArrayKeyCase($item, $case);
        } else {
            $new[$keyTemp] = $item;
        }
    }

    return $new;
}

/**
 *  utf8=>gbk
 * @author:wkj
 * @date  2017/5/2 13:19
 * @param $str
 * @return string
 */
function iconv_to_gbk($str){
    $str_utf_8 = '';

    if (mb_detect_encoding($str, array(
            'UTF-8',
            'GBK'
        )) == 'UTF-8'
    ) {
        $str_utf_8 = iconv("UTF-8", "GBK//IGNORE", $str);
    }

    if ($str_utf_8 != '') {
        $str = $str_utf_8;
    }

    return $str;
}

/**
 *  写日志
 * @author:wkj
 * @date  2017/5/2 13:22
 * @param        $log
 * @param string $type
 */
function writeLog($log, $type = 'sql'){
    $filename = './' . date("Ymd") . '_' . $type . ".log";
    @$handle = fopen($filename, "a+");
    @fwrite($handle, date('Y-m-d H:i:s') . "  " . $log . "\r\n");
    @fclose($handle);
}

/**
 *  判断是否是utf-8
 * @author:wkj
 * @date  2017/5/2 13:22
 * @param $word
 * @return bool
 */
function is_utf8($word){
    if (preg_match("/^([" . chr(228) . "-" . chr(233) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}){1}/", $word) == true || preg_match("/([" . chr(228) . "-" . chr(233) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}){1}$/", $word) == true || preg_match("/([" . chr(228) . "-" . chr(233) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}){2,}/", $word) == true) {
        return true;
    } else {
        return false;
    }
}

function getIP(){
    if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
        $ip = getenv("HTTP_CLIENT_IP"); else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
        $ip = getenv("HTTP_X_FORWARDED_FOR"); else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
        $ip = getenv("REMOTE_ADDR"); else if (isset($_SERVER["REMOTE_ADDR"]) && $_SERVER["REMOTE_ADDR"] && strcasecmp($_SERVER["REMOTE_ADDR"], "unknown"))
        $ip = $_SERVER["REMOTE_ADDR"]; else
        $ip = "unknown";

    return ($ip);
}

function isIp($ip){
    if (preg_match('/^((?:(?:25[0-5]|2[0-4]\d|((1\d{2})|([1-9]?\d)))\.){3}(?:25[0-5]|2[0-4]\d|((1\d{2})|([1-9]?\d))))$/', $ip)) {
        return true;
    } else {
        return false;
    }
}

/**
 * 星座
 * @author:wkj
 * @date  2017/5/2 13:24
 * @param $month
 * @param $day
 * @return bool
 */
function get_zodiac_sign($month, $day){
    // 检查参数有效性
    if ($month < 1 || $month > 12 || $day < 1 || $day > 31) {
        return (false);
    }

    // 星座名称以及开始日期
    $signs = array(
        array("20" => "宝瓶座"),
        array("19" => "双鱼座"),
        array("21" => "白羊座"),
        array("20" => "金牛座"),
        array("21" => "双子座"),
        array("22" => "巨蟹座"),
        array("23" => "狮子座"),
        array("23" => "处女座"),
        array("23" => "天秤座"),
        array("24" => "天蝎座"),
        array("22" => "射手座"),
        array("22" => "摩羯座")
    );
    list($sign_start, $sign_name) = each($signs[(int)$month - 1]);
    if ($day < $sign_start)
        list($sign_start, $sign_name) = each($signs[($month - 2 < 0) ? $month = 11 : $month -= 2]);

    return $sign_name;
}

/*
@Func:按字符截取字符串(utf8)
@Auth:wkj
@Date:2016/12/5
@Param:
	$start:开始的位置
	$length:截取长度 (length不需要乘以3)
*/
function msubstr_just($str, $start, $length){
    $i = 0;
    $j = 0;
    //完整排除之前的UTF8字符
    while ($i < $start) {
        $ord = ord($str{$i});
        if ($ord < 192) {
            $i++;
        } elseif ($ord < 224) {
            $i += 2;
        } else {
            $i += 3;
        }
    }
    //开始截取
    $result = '';
    while ($j < $start + $length && $i < strlen($str)) {
        $ord = ord($str{$i});
        if ($ord < 192) {
            $result .= $str{$i};
            $i++;
        } elseif ($ord < 224) {
            $result .= $str{$i} . $str{$i + 1};
            $i += 2;
        } else {
            $result .= $str{$i} . $str{$i + 1} . $str{$i + 2};
            $i += 3;
        }
        $j++;
    }

    return $result;
}

/*
 *	@Func	获取GET/POST参数 优先GET
 *	@Author	wkj
 *	@Time	2017/2/17
 *	@Param	$index 索引名称
 *	@Param	$default 默认值
 **/
function get_post($index = null, $default = ''){
    if ($index === null) {
        if (!empty($_GET))
            return $_GET;

        return $_POST;
    }

    return isset($_GET[$index]) ? $_GET[$index] : (isset($_POST[$index]) ? $_POST[$index] : $default);
}

/*
 *	@Func		生成验证码图片 $_SESSION['touch_img_code']
 *	@Author		wkj
 *	@CreateTime	2016/9/9
 *  @Param 		$wdith 宽度
 *  @Param 		$height 高度
 *  @Param 		$count 长度
 *  @UpdateTime
 **/
function imgCode($width = 120, $height = 25, $count = 4){
    $randnum = "";
    if (function_exists("imagecreatetruecolor") && function_exists("imagecolorallocate") && function_exists("imagestring") && function_exists("imagepng") && function_exists("imagesetpixel") && function_exists("imagefilledrectangle") && function_exists("imagerectangle")) {
        $image = imagecreatetruecolor($width, $height);
        $swhite = imagecolorallocate($image, 255, 255, 255);
        $sblack = imagecolorallocate($image, 0, 0, 0);
        imagefilledrectangle($image, 0, 0, $width, $height, $swhite);
        imagerectangle($image, 0, 0, $width, $height, $swhite);
        $i = 0;
        for (; $i < 10; ++$i) {
            $sjamcolor = imagecolorallocate($image, rand(0, 120), rand(0, 120), rand(0, 120));
            imagesetpixel($image, rand(0, $width), rand(0, $height), $sjamcolor);
        }
        $i = 0;
        for (; $i < $count; ++$i) {
            //$randnum .= dechex( rand( 1, 15 ) );
        }
        $randval = '';
        mt_srand((double)microtime() * 1000000);
        for ($i = 0; $i < $count; $i++) {
            $randval .= mt_rand(0, 9);
        }

        $randnum = $randval;
        $_SESSION['touch_img_code'] = $randnum;


        $widthx = floor($width / $count);
        $i = 0;
        for (; $i < strlen($randnum); ++$i) {
            $irandomcolor = imagecolorallocate($image, rand(50, 120), rand(50, 120), rand(50, 120));
            imagestring($image, 5, $widthx * $i + rand(3, 5), rand(3, 5), $randnum[$i], $irandomcolor);
        }

        ob_clean();
        header("Pragma:no-cache");
        header("Cache-control:no-cache");
        header("Content-type: image/png");
        imagepng($image);
        imagedestroy($image);

        return $randnum;
    }
}


/**
 *  正则 汉字 中文
 * @author:wkj
 * @date  2017/5/2 13:31
 * @param $username
 * @return bool
 */
function checkChinese($username){
    if (!preg_match('/^[\x{4e00}-\x{9fa5}]{2,5}$/u', $username)) {
        return false;
    }

    return true;
}