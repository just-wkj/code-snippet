<?php

/**
 * CURL 工具类 get post
 * Author: wkj
 * Contact: 602823863@qq.com
 * Date: 2017/06/01
 * Time: 14:44
 */
class Curl{
    /**
     *  get请求
     * @author:wkj
     * @date  2017/6/1 14:48
     * @param       $url      请求地址
     * @param array $headers  请求头
     * @param int   $timeOut  超时时间
     * @param bool  $isFollow 302跳转跟随
     * @return bool|mixed
     */
    public static function get($url, $headers = array(), $timeOut = 10, $isFollow = false){
        $ch = curl_init();
        if (stripos($url, "https://") !== false) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSLVERSION, 1);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeOut);
        if ($isFollow) {
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        }
        if ($headers) {
            $_header = array();
            foreach ($headers as $key => $vo) {
                $_header[] = $key . ':' . $vo;
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $_header);
        }
        $sContent = curl_exec($ch);
        $aStatus = curl_getinfo($ch);
        curl_close($ch);

        if (intval($aStatus["http_code"]) == 200) {
            return $sContent;
        } else {
            return false;
        }


    }

    /**
     *  post请求
     * @author:wkj
     * @date  2017/6/1 14:49
     * @param       $url     请求地址
     * @param array $post    post数据
     * @param array $headers 请求头
     * @param int   $timeOut 超时时间
     * @return mixed
     */
    public static function post($url, array $post = array(), $headers = array(), $timeOut = 10){
        $defaults = array(
            CURLOPT_POST           => 1,
            CURLOPT_HEADER         => 0,
            CURLOPT_URL            => $url,
            CURLOPT_FRESH_CONNECT  => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE   => 1,
            CURLOPT_TIMEOUT        => $timeOut,
            CURLOPT_POSTFIELDS     => http_build_query($post)
        );

        $ch = curl_init();
        curl_setopt_array($ch, $defaults);
        if ($headers) {
            $_header = array();
            foreach ($headers as $key => $vo) {
                $_header[] = $key . ':' . $vo;
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $_header);
        }
        if (!$result = curl_exec($ch)) {
            trigger_error(curl_error($ch));
        }
        curl_close($ch);

        return $result;
    }

    /**
     *  get请求针对接口json数据处理为数组
     * @author:wkj
     * @date  2017/6/1 14:49
     * @param       $url     请求地址
     * @param int   $timeOut 超时时间
     * @param array $headers 请求头
     * @return bool|mixed
     */
    public static function getArray($url, $timeOut = 10, $headers = array()){
        $result = self::get($url, $timeOut, $headers);
        if ($result) {
            return json_decode($result, true);
        }

        return false;
    }
}