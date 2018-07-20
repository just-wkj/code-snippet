<?php
/**
 * 缓存类自行更换,APP_ID,APP_SECRET设置
 * @author     :wkj
 * @createTime :2018/7/11 10:19
 * @description: 微信模板消息处理
 */



class JustWechat{
    private $appId;
    private $appSecret;
    private $nocestr = 'dfsg23%^&#';
    private $expire = 7000;

    public function __construct(){
        $this->appId = Env::get('APP_ID');
        $this->appSecret = Env::get('APP_SECRET');
    }

    private function getCacheKey(){
        return md5($this->nocestr . $this->appId . $this->appSecret);
    }

    public function getAccessToken(){
        $accessToken = Cache::get($this->getCacheKey());
        if ($accessToken) {
            return $accessToken;
        }
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s';
        $url = sprintf($url, $this->appId, $this->appSecret);
        $res = JustCurl::getArray($url);
        if ($res && is_array($res) && isset($res['access_token'])) {
            Cache::set($this->getCacheKey(), $res['access_token'], $this->expire);
        }

        return $res['access_token'];
    }


    /**
     *  获取当前小程序的的模板消息列表
     * @author:wkj
     * @date 2018/7/11 11:02
     * @param int $offset
     * @param int $count
     * @return mixed
     */
    private function getMsgTemplateLists($offset = 0, $count = 20){
        $res = JustCurl::postJson(JustCurl::buildUrl('https://api.weixin.qq.com/cgi-bin/wxopen/template/list', [
            'access_token' => $this->getAccessToken(),
        ]), [
            'offset' => $offset,
            'count'  => $count,
        ]);

        return $res;
    }

    //官网复制的模板数组格式提交即可
    //{
    //"touser": "OPENID",
    //"template_id": "TEMPLATE_ID",
    //"page": "index",
    //"form_id": "FORMID",
    //"data": {
    //"keyword1": {
    //"value": "339208499"
    //},
    //"keyword2": {
    //    "value": "2015年01月05日 12:30"
    //      },
    //      "keyword3": {
    //    "value": "粤海喜来登酒店"
    //      } ,
    //      "keyword4": {
    //    "value": "广州市天河区天河路208号"
    //      }
    //  },
    //  "emphasis_keyword": "keyword1.DATA"
    //}
    public function send(array $data){
        $res = JustCurl::postJson(JustCurl::buildUrl('https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send', [
            'access_token' => $this->getAccessToken(),
        ]), $data);
        writeLog($res, 'wx_msg');

        return $res;
    }

    /**
     *  模板消息数据格式生成
     * @author:wkj
     * @date 2018/7/11 11:20
     * @param array $data
     * @return array
     */
    public function buildSendData($data = []){
        $send = [];
        $send['touser'] = isset($data['touser']) ? $data['touser'] : '';
        $send['template_id'] = isset($data['template_id']) ? $data['template_id'] : '';
        if (isset($data['page'])) {
            $send['page'] = $data['page'];
        }
        $send['form_id'] = isset($data['form_id']) ? $data['form_id'] : '';
        foreach (range(1, 20) as $num) {
            $_key = 'keyword' . $num;
            if (isset($data[$_key])) {
                $send['data'][$_key] = ['value' => $data[$_key]];
            }
        }

        return $send;
    }


    /**
     *  生成二维码
     * @author:wkj
     * @date 2018/7/19 15:20
     * @param $id
     * @return mixed
     */
    public function buildImg($id){
        $url = 'https://api.weixin.qq.com/wxa/getwxacode?access_token=' . $this->getAccessToken();
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => "POST",
            CURLOPT_POSTFIELDS     => json_encode([
                'path' => 'pages/detail/detail?id=' . $id,
            ]),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSLVERSION     => 1,
        ]);
        $response = curl_exec($curl);
        curl_close($curl);
        $filename = 'tmp.jpeg';
        file_put_contents('./' . $filename, $response);
        $rand = microtime();
        echo '请用微信扫描,查看是否正确';
        echo '<img src="/' . $filename . '?t=' . $rand . '">';
    }
}
