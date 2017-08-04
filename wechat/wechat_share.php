<?php
//微信分享测试
//微信文档 https://mp.weixin.qq.com/wiki?t=resource/res_main
define('APP_ID', 'wx8f0751f23e17069a');
define('APP_SECRECT', 'f2076913c6fb0a418ab7d3bdf438e823');
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
$redis->auth('wkj');

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
            return json_decode($sContent, true);
        } else {
            return false;
        }
    }
}


function getAccessToken($app_id, $app_secret){
    global $redis;
    $key = 'getAccessToken';
    $access_token = $redis->get($key);
    if ($access_token) {
        return $access_token;
    }

    //缓存中读取
    $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $app_id . '&secret=' . $app_secret;
    $return = curlGet($url);
    if ($return) {
        $redis->set($key, $return['access_token'], 7000);

        return $return['access_token'];
    } else {
        echo '接口调用故障!';
        die;
    }
}

function getSignature(){
    $access_token = getAccessToken(APP_ID, APP_SECRECT);
    $key = 'getSignature';
    global $redis;
    $ticket = $redis->get($key);
    if (!$ticket) {
        $url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=' . $access_token . '&type=jsapi';
        $return = curlGet($url);
        if ($return && isset($return['ticket'])) {
            $ticket = $return['ticket'];
            $redis->set($key, $ticket, 7000);
        } else {
            echo '获取签名失败!';
            die;
        }
    }

    $noncestr = 'a' . rand(100000, 9999999);
    $jsapi_ticket = $ticket;
    $timestamp = time();
    $url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
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

$rs = getSignature();
echo "<pre>";
print_r($rs);
echo "</pre>";


?>
<script src="//res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>

<script>
    wx.config({
        debug: true, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
        appId: 'wx8f0751f23e17069a', // 必填，公众号的唯一标识
        timestamp: <?php echo $rs['timestamp']?>, // 必填，生成签名的时间戳
        nonceStr: "<?php echo $rs['noncestr']?>", // 必填，生成签名的随机串
        signature: "<?php echo $rs['signature']?>",// 必填，签名，见附录1
        jsApiList: ['onMenuShareTimeline', 'onMenuShareAppMessage'] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
    });

    wx.onMenuShareTimeline({
        title: '测试', // 分享标题
        link: 'https://blog.moyixi.cn/test.php', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
        imgUrl: 'https://aizuna.house365.com/upload_wx_images/2017/07/16/5277c33097006330074665744427e417.jpg', // 分享图标
        success: function () {
            // 用户确认分享后执行的回调函数
        },
        cancel: function () {
            // 用户取消分享后执行的回调函数
        }
    });
    wx.onMenuShareAppMessage({
        title: '测试', // 分享标题
        desc: '测试描述', // 分享描述
        link: 'https://blog.moyixi.cn/test.php', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
        imgUrl: 'https://aizuna.house365.com/upload_wx_images/2017/07/16/5277c33097006330074665744427e417.jpg', // 分享图标
        type: '', // 分享类型,music、video或link，不填默认为link
        dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
        success: function () {
            // 用户确认分享后执行的回调函数
        },
        cancel: function () {
            // 用户取消分享后执行的回调函数
        }
    });
</script>
