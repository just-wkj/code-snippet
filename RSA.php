<?php
/**
 * @author     :wkj
 * @createTime :2018/4/17 15:06
 * @description:
 *
 * 生成rsa公钥和私钥
 * openssl.exe
 * OpenSSL> genrsa -out rsa_private_key.pem 1024
 * OpenSSL> rsa -in rsa_private_key.pem -pubout -out rsa_public_key.pem
 */

class RSA{
    private static $rsa_private_key = './rsa_private_key.pem';
    private static $rsa_public_key = './rsa_public_key.pem';

    private static function checkNesseryFileExists($type = 0){
        if (!is_file(self::$rsa_private_key) || !is_file(self::$rsa_public_key)) {
            echo "缺少必要的加密文件!";
            exit;
        }

        return $type == 0 ? self::$rsa_private_key : self::$rsa_public_key;
    }

    /**
     *  生成加密字符串
     * @author:wkj
     * @date  2018/4/17 14:54
     * @param $string
     * @return string
     */
    public static function encrypt($string){
        $priKey = file_get_contents(self::checkNesseryFileExists());

        /* 从PEM文件中提取私钥 */
        $res = openssl_get_privatekey($priKey);

        /* 对数据进行签名 */
        //openssl_sign($data, $sign, $res);
        openssl_private_encrypt($string, $sign, $res);

        /* 释放资源 */
        openssl_free_key($res);

        /* 对签名进行Base64编码，变为可读的字符串 */
        $sign = base64_encode($sign);

        return $sign;
    }


    /**
     *  解密加密字符串
     * @author:wkj
     * @date  2018/4/17 14:54
     * @param $string
     * @return mixed
     */
    public static function decrypt($string){
        /* 获取公钥PEM文件内容，$rsaPublicKey是指向公钥PEM文件的路径 */
        $pubKey = file_get_contents(self::checkNesseryFileExists(1));
        /* 从PEM文件中提取公钥 */
        $result = openssl_get_publickey($pubKey);

        /* 对数据进行解密 */
        openssl_public_decrypt(base64_decode($string), $decrypted, $result);

        /* 释放资源 */
        openssl_free_key($result);

        return $decrypted;
    }

    /**
     *  根据传递字符串生成签名
     * @author:wkj
     * @date  2018/4/17 14:55
     * @param $string
     * @return string
     */
    public static function sign($string){
        $priKey = file_get_contents(self::checkNesseryFileExists());
        $res = openssl_get_privatekey($priKey);
        openssl_sign($string, $sign, $res);
        openssl_free_key($res);
        $sign = base64_encode($sign);

        return $sign;
    }


    /**
     *  校验签名和传递的字符串
     * @author:wkj
     * @date  2018/4/17 14:56
     * @param $sign
     * @param $string
     * @return bool
     */
    public static function verify($sign, $string){
        $pubKey = file_get_contents(self::checkNesseryFileExists(1));
        $result = openssl_get_publickey($pubKey);
        $bs = base64_decode($sign);
        $ok = openssl_verify($string, $bs, $result);
        openssl_free_key($result);

        return $ok == 1;
    }
}