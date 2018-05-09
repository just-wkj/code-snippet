<?php
/**
 * @author     :wkj
 * @createTime :2018/5/5 16:19
 * @description:封装一个微信支付的类适可以在此基础上自行的相应修改
 *             设置好下面的参数
 *              pem证书位置设置好
 *             二次签名的时候根据业务定义不用的参数 微信支付和小程序的支付不同看下文档即可 -wkj
 *             writeLog() 记录日志的方法可以全部删除掉
 */

namespace app\api\controller;


class WechatX{
    const APP_ID = 'APP_ID';
    const MCH_ID = 'MCH_ID';
    const MCH_API_KEY = 'MCH_API_KEY';
    const NOTICE_URL = "NOTICE_URL 回调地址/api/Wechat/callBack";
    const TRADE_TYPE = 'JSAPI';//交易类型 小程序是此类型 需要openid,其他的自行查看文档


    /**
     *  微信统一下单
     * @author:wkj
     * @date  2018/5/5 16:58
     * @param      $out_trade_no
     * @param      $money
     * @param null $openid
     * @return array
     */
    public function unifiedorder($out_trade_no, $money, $openid = null){
        $json = array();
        //生成预支付交易单的必选参数:
        $newPara = array();
        //应用ID
        $newPara["appid"] = self::APP_ID;
        //商户号
        $newPara["mch_id"] = self::MCH_ID;
        //设备号
        $newPara["device_info"] = "WEB";
        //随机字符串,这里推荐使用函数生成
        $newPara["nonce_str"] = self::getNonceStr();
        //商品描述
        $newPara["body"] = "支付";
        //商户订单号,这里是商户自己的内部的订单号
        $newPara["out_trade_no"] = $out_trade_no;
        //总金额
        $newPara["total_fee"] = $money;
        //终端IP
        $newPara["spbill_create_ip"] = $_SERVER["REMOTE_ADDR"];
        //通知地址，注意，这里的url里面不要加参数
        $newPara["notify_url"] = self::NOTICE_URL;
        //交易类型
        $newPara["trade_type"] = self::TRADE_TYPE;
        if (self::TRADE_TYPE == 'JSAPI') {
            $newPara["openid"] = $openid;
        }
        //第一次签名
        $newPara["sign"] = self::getSign($newPara);

        //把数组转化成xml格式
        $xmlData = self::arrayToXml($newPara);

        //利用PHP的CURL包，将数据传给微信统一下单接口，返回正常的prepay_id
        $get_data = self::sendPrePayCurl($xmlData);
        writeLog('sendPrePayCurl', 'wx_order_pay');
        writeLog($get_data, 'wx_order_pay');
        //返回的结果进行判断。
        if ($get_data['return_code'] == "SUCCESS" && $get_data['result_code'] == "SUCCESS") {
            //根据微信支付返回的结果进行二次签名
            //二次签名所需的随机字符串
            $wechatData = array(
                "appId"     => $newPara['appid'],
                "nonceStr"  => self::getNonceStr(),
                //"package"   => "Sign=WXPay",
                //"prepayid"  => $get_data['prepay_id'],
                "package" =>"prepay_id=".$get_data['prepay_id'],
               // "partnerid" => $newPara['mch_id'],
                "signType" => 'MD5',
                "timeStamp" => time(),
            );
            $_sign = self::getSign($wechatData);
            $wechatData['sign'] = $_sign;
            $json['wechat'] = $wechatData;
            $json['check_code'] = $newPara["out_trade_no"];
            $json['message'] = "ok";

            //预支付完成,在下方进行自己内部的业务逻辑
            /*****************************/
        } else {
            $json['wechat'] = array();
            $json['check_code'] = $newPara["out_trade_no"];
            $json['message'] = $get_data['return_msg'];
        }

        return $json;
    }

    /**
     *  随机字符串
     * @author:wkj
     * @date  2018/5/5 16:55
     * @return string
     */
    public static function getNonceStr(){
        return md5(uniqid() . microtime() . rand(0, 999999));
    }


    /**
     * 生成签名
     * @author:wkj
     * @date  2018/5/5 16:56
     * @param $params
     * @return string
     */
    public static function getSign($params){
        unset($params['sign']);
        ksort($params);
        $stringA = urldecode(http_build_query($params));
        $stringSignTemp = "$stringA&key=" . self::MCH_API_KEY;
        if (isset($_POST['test'])) {
            echo $stringSignTemp . "\n";
        }

        return strtoupper(md5($stringSignTemp));
    }

    /**
     *  获取微信统一下单所需的prepay_id
     * @author:wkj
     * @date  2018/5/5 16:57
     * @param $xmlData
     * @return array
     */
    public static function sendPrePayCurl($xmlData){
        $url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
        $header[] = "Content-type: text/xml";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $xmlData);

        //添加证书校验
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSLCERTTYPE, 'PEM');
        curl_setopt($curl, CURLOPT_SSLCERT, getcwd() . '/cacert/' . self::APP_ID . '/apiclient_cert.pem');
        //默认格式为PEM，可以注释
        curl_setopt($curl, CURLOPT_SSLKEYTYPE, 'PEM');
        curl_setopt($curl, CURLOPT_SSLKEY, getcwd() . '/cacert/' . self::APP_ID . '/apiclient_key.pem');

        $data = curl_exec($curl);
        if (curl_errno($curl)) {
            print curl_error($curl);
        }
        curl_close($curl);

        return self::xmlToArray($data);

    }

    /**
     *  数组转为xml
     * @author:wkj
     * @date  2018/5/5 16:57
     * @param $newPara
     * @return string
     */
    public static function arrayToXml($newPara){
        $xmlData = "<xml>";
        foreach ($newPara as $key => $value) {
            $xmlData = $xmlData . "<" . $key . ">" . $value . "</" . $key . ">";
        }
        $xmlData = $xmlData . "</xml>";

        return $xmlData;
    }

    //xml格式数据解析函数
    public static function xmlToArray($data){
        $msg = (array)simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA);

        return $msg;
    }

    //微信回调函数设置
    public function callBack(){
        $post = $_REQUEST;
        if($post==null){
            $post = file_get_contents("php://input");
            if($post == null){
                $post = $GLOBALS['HTTP_RAW_POST_DATA'];
            }
        }
        //记录下微信返回
        writeLog($post, 'callBack');
        //TODO 逻辑处理

        $post_data = self::xmlToArray($post);   //微信支付成功，返回回调地址url的数据：XML转数组Array
        writeLog($post_data, 'callBack');
        $postSign = $post_data['sign'];
        unset($post_data['sign']);

        /* 微信官方提醒：
        *  商户系统对于支付结果通知的内容一定要做【签名验证】,
        *  并校验返回的【订单金额是否与商户侧的订单金额】一致，
        *  防止数据泄漏导致出现“假通知”，造成资金损失。
        */
        $user_sign = self::getSign($post_data);
        writeLog('$postSign='.$postSign,'callBack');
        writeLog('$user_sign='.$user_sign,'callBack');
        if($postSign != $user_sign){
            writeLog('签名校验失败','callBack');
            exit;
        }

        if ($post_data['return_code'] == 'SUCCESS' && $postSign) {
            /*
            * 首先判断，订单是否已经更新为ok，因为微信会总共发送8次回调确认
            * 其次，订单已经为ok的，直接返回SUCCESS
            * 最后，订单没有为ok的，更新状态为ok，返回SUCCESS
            */
            //逻辑校验处理..成功的通知微信结果
            if(true){
                $this->returnSuccess();
            }
        }else{
            writeLog('校验失败','callBack');
            echo '微信支付失败';
        }

    }


    /**
     * 给微信发送确认订单金额和签名正确，SUCCESS信息
     */
    private function returnSuccess(){
        echo self::arrayToXml([
            'return_code' => 'SUCCESS',
            'return_msg' => 'OK',
        ]);
        exit;
    }
}
