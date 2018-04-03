<?php
/**
 * @author     :wkj
 * @createTime :2018/4/3 9:51
 * @description: 生成微信分享所需要的基本参数
 */

define('APP_ID', 'appid');
define('APP_SECRECT', '秘钥');
define('DOMAIN', '域名 eg http://wechat.moyixi.cn');
define('CACHE_EXPIRE_TIME', 7000);

$wechatShare = new WechatShare(new FileCache(CACHE_EXPIRE_TIME));
$return = $wechatShare->getSignature();
$return['appId'] = APP_ID;
$return['nonceStr'] = $return['noncestr'];
echo json_encode($return);
die;

class WechatShare{
    private $app_id = APP_ID;
    private $app_secret = APP_SECRET;
    private $cache;

    public function __construct($cache){
        $this->cache = $cache;
    }

    private function getAccessToken(){
        $key = 'getAccessToken';
        $access_token = $this->cache->get($key);
        if ($access_token) {
            return $access_token;
        }

        //缓存中读取
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $this->app_id . '&secret=' . $this->app_secret;
        $return = curlGet($url);
        if ($return) {
            $this->cache->set($key, $return['access_token'], CACHE_EXPIRE_TIME);
            return $return['access_token'];
        } else {
            echo '接口调用故障!';
            die;
        }
    }
    public  function getSignature(){
        $access_token = $this->getAccessToken();
        $key = 'getSignature';
        $ticket = $this->cache->get($key);
        if (!$ticket) {
            $url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=' . $access_token . '&type=jsapi';
            $return = $this->curlGet($url);
            if ($return && isset($return['ticket'])) {
                $ticket = $return['ticket'];
                $this->cache->set($key, $ticket, CACHE_EXPIRE_TIME);
            } else {
                echo '获取签名失败!';
                die;
            }
        }

        $noncestr = 'a' . rand(100000, 9999999);
        $jsapi_ticket = $ticket;
        $timestamp = time();
        if (!isset($_POST['url']) || !$_POST['url']) {
            echo json_encode(array(
                'error' => 'invalid url'
            ));
            die;
        }
        $url = urldecode($_POST['url']);
        $arr = array(
            'noncestr'     => $noncestr,
            'jsapi_ticket' => $jsapi_ticket,
            'timestamp'    => $timestamp,
            'url'          => $url,
        );
        ksort($arr);
        $str = '';
        foreach ($arr as $key => $vo) {
            $str .= "$key=$vo&";
        }
        $arr['signature'] = sha1(trim($str, '&'));

        return $arr;
    }

    private function curlGet($url, $timeOut = 10){
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
            return json_decode($sContent, true);
        } else {
            return false;
        }
    }
}

//文件缓存
class FileCache{
    private $cache_path;//缓存路径
    private $cache_expire;//缓存时间

    public function __construct($exp_time = 3600, $path = "cache/"){
        $this->cache_expire = $exp_time;
        $this->cache_path = $path;
        if (!is_dir($path)) {
            mkdir($path, 0777, 1);
        }
    }

    private function fileName($key){
        return $this->cache_path . md5($key);
    }

    public function set($key, $data){
        $values = serialize($data);
        $filename = $this->fileName($key);
        $file = fopen($filename, 'w');
        if ($file) {
            fwrite($file, $values);
            fclose($file);
        } else {
            return false;
        }
    }

    public function get($key){
        $filename = $this->fileName($key);
        if (!file_exists($filename) || !is_readable($filename)) {
            return false;
        }
        if (time() < (filemtime($filename) + $this->cache_expire)) {
            $file = fopen($filename, "r");
            if ($file) {
                $data = fread($file, filesize($filename));
                fclose($file);

                return unserialize($data);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}