<?php
/**
 * author:wkj
 * createTime:2017/4/24 6:59
 * description:
 */

header("Content-type:text/html;charset=utf-8");

//define('APP_ID', 'xx');
//define('APP_SECRET', 'yyy');

define('APP_ID','xxx');
define('APP_SECRET','yyy');

define('REDIRECT_URI', 'http://www.xxxx.com/wechat/index.php');
define('INDEX_URL', 'http://www.xxxx.com');


include_once './medoo.php';
$db = new medoo(array(
	// 必须配置项
	'database_type' => 'mysql',
	'database_name' => 'ggd_db',
	'server'        => 'localhost',
	'username'      => 'root',
	'password'      => 'xxxx',
	'charset'       => 'utf8',
	// 可选参数
	'port'          => 3306,

	// 连接参数扩展, 更多参考 http://www.php.net/manual/en/pdo.setattribute.php
	'option'        => array(
		PDO::ATTR_CASE => PDO::CASE_NATURAL
	)
));
#############
//$access_token = getAccessToken();
//$qrStr = '{"action_name": "QR_LIMIT_STR_SCENE", "action_info": {"scene": {"scene_str": "123"}}}';
//$rs = curlPost('https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$access_token,$qrStr);
//p($rs);
//die;
#############

//获取code
if (!isset($_GET['code'])) {
	$getCode = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . APP_ID . "&redirect_uri=" . urlencode(REDIRECT_URI) . "&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
	header("location:$getCode");
	die;
}
$code = $_GET['code'];
//获取网页授权的access_token
$getToken = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . APP_ID . "&secret=" . APP_SECRET . "&code=$code&grant_type=authorization_code";
$rs = curlGet($getToken);
$tokenArr = json_decode($rs, true);
$access_token = $tokenArr['access_token'];

//防止意外若是不存在再去请求
if (!isset($tokenArr['openid'])) {
	$getCode = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . APP_ID . "&redirect_uri=" . urlencode(REDIRECT_URI) . "&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
	header("location:$getCode");
	die;
}
$openid = $tokenArr['openid'];

//获取用户资料
$getUserInfo = "https://api.weixin.qq.com/sns/userinfo?access_token=$access_token&openid=$openid&lang=zh_CN";
$rs = curlGet($getUserInfo);
$userInfo = json_decode($rs, true);

$nickname = $userInfo['nickname'];
$sex = $userInfo['sex'];
$city = $userInfo['city'];
$province = $userInfo['province'];
$country = $userInfo['country'];
$avatar = $userInfo['headimgurl'];
$curTime = time();
$rs = $db->insert("fdc_wechat", array(
	"openid"          => $openid,
	"nickname"        => $nickname,
	"sex"             => $sex,
	"city"            => $city,
	"province"        => $province,
	"country"         => $country,
	"avatar"          => $avatar,
	"add_time"        => $curTime,
	"last_login_time" => $curTime
));

header('location:' . INDEX_URL);

/**
 * curl Post 请求
 * @param        $url
 * @param        $data
 * @param string $referer
 * @return mixed
 */
function curlPost($url, $data, $referer = '') {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);    //请求URL
	curl_setopt($ch, CURLOPT_HEADER, 0);
	if ($referer) {
		curl_setopt($ch, CURLOPT_REFERER, $referer);    //制造假的REFERER
	}
	curl_setopt($ch, CURLOPT_POST, 0);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);                    //设置参数数据
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);        //返回结果
	$result = curl_exec($ch);
	curl_close($ch);

	return $result;
}

/**
 * HTTP Get操作
 * @param $url
 * @return bool|mixed
 */
function curlGet($url) {
	$oCurl = curl_init();
	if (stripos($url, "https://") !== false) {
		curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($oCurl, CURLOPT_SSLVERSION, 1);
	}
	curl_setopt($oCurl, CURLOPT_URL, $url);
	curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($oCurl, CURLOPT_TIMEOUT, 10);
	$sContent = curl_exec($oCurl);
	$aStatus = curl_getinfo($oCurl);
	curl_close($oCurl);
	if (intval($aStatus["http_code"]) == 200) {
		return $sContent;
	} else {
		return false;
	}
}

/**
 *    打印函数
 * @date 2017/3/23 20:56
 * @param string $data
 * @param int    $is_die
 */
function p($data = 'godie', $is_die = 1) {
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

/**
 *	获取access-token
 * @author:wkj
 * @date 2017/4/25 22:23
 * @return mixed
 */
function getAccessToken(){
	global $db;
	$tokenInfo = $db->get('fdc_wechat_access_token',array('access_token','expires_time'),array('id' => 1));
	if($tokenInfo['access_token'] && $tokenInfo['expires_time'] > time()){
		return $tokenInfo['access_token'];
	} else {
		$getToken = curlGet("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".APP_ID."&secret=".APP_SECRET);
		$json = json_decode($getToken,true);
		$access_token = $json['access_token'];
		if(!$tokenInfo ){
			$db->insert("fdc_wechat_access_token", array(
				"access_token"          => $access_token,
				"expires_time"        => time()+7000
			));
		} else {
			$db->update("fdc_wechat_access_token", array(
				"access_token"          => $access_token,
				"expires_time"        => time()+7000
			),array(
				'id' => 1
			));
		}
		return $access_token;
	}
}