<?php
/**
 * author:wkj
 * createTime:2017/4/23 19:14
 * description:
 */

define('TOKEN', 'f2dSecretToken');
define('WECHAT_NAME','莫一兮');
//define('WECHAT_APP_ID','xxx');
//define('WECHAT_APP_SECRET','yyy');
define('WECHAT_APP_ID','xxxxx');
define('WECHAT_APP_SECRET','yyyyy');

function writelog($log,$type='sql'){
	$filename = './'.date("Ymd").'_'.$type.".log";
	@$handle=fopen($filename, "a+");
	@fwrite($handle, date('Y-m-d H:i:s')."  ".$log."\r\n");
	@fclose($handle);
}
$obj = new Weixin();
if (!isset($_GET['echostr'])) {
	$obj->receive();
} else {
	$obj->checkSignature();
}

class   Weixin {

	public function checkSignature() {
		$signature = $_GET["signature"];   //加密签名
		$timestamp = $_GET["timestamp"]; //时间戳
		$nonce = $_GET["nonce"];    //随机数
		$token = TOKEN; //token

		$tmpArr = array($token, $timestamp, $nonce);//组成新数组
		sort($tmpArr, SORT_STRING);//重新排序
		$tmpStr = implode($tmpArr);//转换成字符串
		$tmpStr = sha1($tmpStr);  //再将字符串进行加密

		if ($tmpStr == $signature) {

			echo $_GET['echostr'];
		} else {
			return false;
		}
	}


	public function receive() {
		//$obj = $GLOBALS['HTTP_RAW_POST_DATA'];//已经被废弃了
		$obj = file_get_contents("php://input");
		$postSql = simplexml_load_string($obj, 'SimpleXMLElement', LIBXML_NOCDATA);
		$this->logger("接受：\n" . $obj);

		if (!empty($postSql)) {
			switch (trim($postSql->MsgType)) {
				case "text" :
					$result = $this->receiveText($postSql);
					break;
				case "event":
					$this->logger("接受：event" );
					if ($postSql->EventKey == 'bt_cet'){
						$result = $this->returnText($postSql,$msg = "如需查询四六级,请输入如下格式:\n
					姓名 空格 准考证号 \n
					如: 张三 1234567890
				");
					} else {
						$result = $this->subscribe($postSql);
					}
					break;
			}
			if (!empty($result)) {
				echo $result;
			} else {
				//无意义的不回答
				$xml = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[%s]]></MsgType>
						<Content><![CDATA[%s]]></Content>
					  </xml>";
				//echo $result = sprintf($xml, $postSql->FromUserName, $postSql->ToUserName, time(), $postSql->MsgType, "没有这条文本消息");
			}
		}
	}

	private function subscribe($postSql){
		$xml = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[%s]]></MsgType>
						<Content><![CDATA[%s]]></Content>
					  </xml>";
		$msg = "欢迎关注".WECHAT_NAME;
		$result = sprintf($xml, $postSql->FromUserName, $postSql->ToUserName, time(), 'text', $msg);
		return $result;
	}

	private  function returnText($postSql,$msg=''){
		$xml = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[%s]]></MsgType>
						<Content><![CDATA[%s]]></Content>
					  </xml>";
		return $result = sprintf($xml, $postSql->FromUserName, $postSql->ToUserName, time(), $postSql->MsgType = 'text', $msg);
	}
	private function receiveText($postSql) {
		$content = trim($postSql->Content);
		if(preg_match('/^([\x{4e00}-\x{9fa5}]{2,5})\s*(\d+)$/u',$content, $out) ){

			//
			//$result = sprintf($xml, $postSql->FromUserName, $postSql->ToUserName, time(), $postSql->MsgType, "查询四六级结果:\n $content");
		} else if (strstr($content, "四六级")) {
			$xml = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[%s]]></MsgType>
						<Content><![CDATA[%s]]></Content>
					  </xml>";
			$msg = "如需查询四六级,请输入如下格式:\n
					姓名 空格 准考证号 \n
					如: 张三 1234567890
				";
			$result = sprintf($xml, $postSql->FromUserName, $postSql->ToUserName, time(), $postSql->MsgType, $msg);
		} else if (strstr($content, "单图文")) {
			$result = $this->receiveImage($postSql);
		} else if (strstr($content, "课表") || strstr($content, "课程表")){
			$result = $this->receiveImage($postSql);
		}
		$this->logger("发送单图文消息：\n" . $result);

		return $result;
	}

	private function receiveImage($postSql) {
		$xml = "<xml>
			<ToUserName><![CDATA[%s]]></ToUserName>
			<FromUserName><![CDATA[%s]]></FromUserName>
			<CreateTime>%s</CreateTime>
			<MsgType><![CDATA[%s]]></MsgType>
			<ArticleCount>1</ArticleCount>
			<Articles>
			<item>
			<Title><![CDATA[%s]]></Title>
			<Description><![CDATA[%s]]></Description>
			<PicUrl><![CDATA[%s]]></PicUrl>
			<Url><![CDATA[%s]]></Url>
			</item>
			</Articles>
			</xml> ";
		$result = sprintf($xml, $postSql->FromUserName, $postSql->ToUserName, time(), "news", "我的课程表", "点击查看我的课程表吧!", "https://ss2.bdstatic.com/70cFvnSh_Q1YnxGkpoWK1HF6hhy/it/u=2792694543,2950372743&fm=23&gp=0.jpg", "http://f2d.moyixi.cn/wx/wechat_course_list.php?".$postSql->ToUserName);

		return $result;
	}

	private  function setMenu(){
		$tokenReturn = curlGet("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".WECHAT_APP_ID."&secret=".WECHAT_APP_SECRET);
		$tokenObj = json_decode($tokenReturn,true);
		$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token={$tokenObj['access_token']}";
		$data =<<<php
{
     "button":[
				 {
					   "name":"信息查询",
					   "sub_button":[
					   {	
						   "type":"view",
						   "name":"课表查询",
						   "url":"http://f2d.moyixi.cn/wx/wechat_course_list.php"
						},
						{	
						   "type":"view",
						   "name":"讲座信息",
						   "url":"http://f2d.moyixi.cn/wx/wechat_trainning_list.php"
						},
						{	
						   "type":"view",
						   "name":"通讯录",
						   "url":"http://f2d.moyixi.cn/wx/wechat_teacher_list.php"
						}
						]
       			},
				 {
					   "name":"成绩查询",
					   "sub_button":[
					   {	
						   "type":"view",
						   "name":"期末成绩",
						   "url":"http://f2d.moyixi.cn/wx/wechat_score_list.php"
						},
						{	
							  "type":"click",
							  "name":"四六级成绩",
							  "key":"bt_cet"
						  }
						]
       			},
				{
					   "name":"互动区",
					   "sub_button":[
					   {	
						   "type":"view",
						   "name":"场地预定",
						   "url":"http://f2d.moyixi.cn/wx/wechat_room_list.php"
						},
						{	
						   "type":"view",
						   "name":"留言板",
						   "url":"http://f2d.moyixi.cn/wx/wechat_message_list.php"
						},
						{	
						   "type":"view",
						   "name":"失物招领",
						   "url":"http://f2d.moyixi.cn/wx/wechat_lose_list.php"
						}
						]
       			}
       ]
 }
php;

		$rs = curlPost($url,$data);
		echo $rs;
		die;
	}

	private function logger($content) {
		$logSize = 100000;
		$log = "log.txt";
		if (file_exists($log) && filesize($log) > $logSize) {
			//unlink($log);
		}
		//file_put_contents($log, date('H:i:s') . " " . $content . "\n", FILE_APPEND);
	}


}

