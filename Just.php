<?php
/**
 * @author     :wkj
 * @createTime :2018/11/18 11:05
 * @description:
 */

class Just{
    public static function changeArrayKeyCase(array $data, $case = CASE_LOWER){
        $new = [];
        foreach ($data as $key => $item) {
            if ($case == CASE_UPPER) {
                $keyTemp = strtoupper($key);
            } else {
                $keyTemp = strtolower($key);
            }

            if (is_array($item)) {
                $new[$keyTemp] = self::changeArrayKeyCase($item, $case);
            } else {
                $new[$keyTemp] = $item;
            }
        }

        return $new;
    }

    public static function writeLog($log, $type = 'sql'){
        $path = './log/';
        if (!is_dir($path) && !mkdir($path, 0755, true)) {
            //无权创建文件忽略函数
            return false;
        }
        if (is_array($log)) {
            $log = json_encode($log);
        }
        $filename = $path . date("Ymd") . '_' . $type . ".log";
        @$handle = fopen($filename, "a+");
        @fwrite($handle, date('Y-m-d H:i:s') . "  " . $log . "\r\n");
        @fclose($handle);
    }

    /*
    @Func:按字符截取字符串(utf8)
    @Auth:wkj
    @Date:2016/12/5
    @Param:
        $start:开始的位置
        $length:截取长度 (length不需要乘以3)
    */
    public static function msubstr_just($str, $start, $length){
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

    /**
     *  字符串长度截取替换
     * @author:wkj
     * @date  2018/1/10 16:33
     * @param        $string
     * @param        $length
     * @param string $replacement
     * @return mixed
     */
    public static function textLimit($string, $length, $replacement = '...'){
        if (mb_strlen($string) > $length) {
            $string = self::msubstr_just($string, 0, $length) . $replacement;
        }

        return $string;
    }

    /**
     *  正则 汉字 中文
     * @author:wkj
     * @date  2017/5/2 13:31
     * @param $username
     * @return bool
     */
    public static function checkChinese($username){
        if (!preg_match('/^[\x{4e00}-\x{9fa5}]{2,5}$/u', $username)) {
            return false;
        }

        return true;
    }
}
