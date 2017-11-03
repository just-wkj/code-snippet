<?php
/**
 * 微信支付样例 app支付
 * User: wangkeji
 * Date: 2017/11/1
 * Time: 22:20
 */

class WechatPay{
    const OPEN_APP_ID = '开放平台的id';
    const MCH_ID = '商户平台的id';
    const MCH_SECRET = '商户平台设置的秘钥';
    const NOTICE_URL = "微信回调地址";

    private $body = "支付标题";
    private $out_trade_no = '订单编号';
    private $total_fee = '支付金额单位分';

    /**
     * 返回客户端支付信息
     * @return mixed
     */
    public function index(){
        //TODO 业务逻辑逻辑处理 ...
        //TODO 业务逻辑逻辑处理 ...

        //支付调用开始 设置必要参数
        //支付标题
        $this->body = 'APP支付测试';
        //订单编号
        $this->out_trade_no = '201700000001';
        //支付金额设置(分)
        $this->total_fee = 1000;
        return $this->weChatPay();
    }

    //入口函数
    private function weChatPay(){
        $return = $newPara = array();
        //生成预支付交易单的必选参数:
        //应用ID
        $newPara["appid"] = self::OPEN_APP_ID;
        //商户号
        $newPara["mch_id"] = self::MCH_ID;
        //设备号
        $newPara["device_info"] = "WEB";
        //随机字符串,这里推荐使用函数生成
        $newPara["nonce_str"] = self::getNonceStr();
        //商品描述
        $newPara["body"] = $this->body;
        //商户订单号,这里是商户自己的内部的订单号
        $newPara["out_trade_no"] = $this->out_trade_no;
        //总金额
        $newPara["total_fee"] = $this->total_fee;
        //终端IP
        $newPara["spbill_create_ip"] = $_SERVER["REMOTE_ADDR"];
        //通知地址，注意，这里的url里面不要加参数
        $newPara["notify_url"] = self::NOTICE_URL;
        //交易类型
        $newPara["trade_type"] = "APP";
        //第一次签名
        $newPara["sign"] = self::getSign($newPara);
        //把数组转化成xml格式
        $xmlData = self::getWeChatXML($newPara);
        //利用PHP的CURL包，将数据传给微信统一下单接口，返回正常的prepay_id
        $get_data = self::sendPrePayCurl($xmlData);
        //返回的结果进行判断。
        if ($get_data['return_code'] == "SUCCESS" && $get_data['result_code'] == "SUCCESS") {
            $wechatData = array(
                "appid"     => $newPara['appid'],
                "noncestr"  => self::getNonceStr(),
                "package"   => "Sign=WXPay",
                "prepayid"  => $get_data['prepay_id'],
                "partnerid" => $newPara['mch_id'],
                "timestamp" => time(),
            );
            //根据微信支付返回的结果进行二次签名,二次签名需要的参数是这6个参数
            $_sign = self::getSign($wechatData);
            $wechatData['sign'] = $_sign;
            $return['wechat'] = $wechatData;
            $return['check_code'] = $newPara["out_trade_no"];
            $return['message'] = "ok";
        } else {
            $return['wechat'] = array();
            $return['check_code'] = $newPara["out_trade_no"];
            $return['message'] = $get_data['return_msg'];
        }

        return $return;
    }

    /**
     *  获取随机字符串
     * @author:wkj
     * @date  2017/11/3 9:41
     * @return string
     */
    public static function getNonceStr(){
        return md5(uniqid() . microtime() . rand(0, 999999));
    }


    /**
     *  签名算法
     * @author:wkj
     * @date  2017/11/3 9:41
     * @param $params
     * @return string
     */
    public static function getSign($params){
        unset($params['sign']);
        ksort($params);
        $stringA = urldecode(http_build_query($params));
        $stringSignTemp = "$stringA&key=" . self::MCH_SECRET;

        return strtoupper(md5($stringSignTemp));
    }


    /**
     *  微信接口调用
     * @author:wkj
     * @date  2017/11/3 9:41
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
        $data = curl_exec($curl);
        if (curl_errno($curl)) {
            echo curl_error($curl);
            exit;
        }
        curl_close($curl);

        return self::XMLDataParse($data);

    }


    /**
     *  数组转化XML
     * @author:wkj
     * @date  2017/11/3 9:41
     * @param $newPara
     * @return string
     */
    public static function getWeChatXML($newPara){
        $xmlData = "<xml>";
        foreach ($newPara as $key => $value) {
            $xmlData = $xmlData . "<" . $key . ">" . $value . "</" . $key . ">";
        }
        $xmlData = $xmlData . "</xml>";

        return $xmlData;
    }

    /**
     *  XML转化数组
     * @author:wkj
     * @date  2017/11/3 9:42
     * @param $data
     * @return array
     */
    public static function XMLDataParse($data){
        $msg = (array)simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA);

        return $msg;
    }
}