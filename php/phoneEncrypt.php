<?php
/**
 * @author     :wkj
 * @createTime :2019/3/1 15:30
 * @email      :justwkj@gmail.com
 * @description: 一个简单的手机号码加密
 * 加密算法简单介绍
 * 第1位是随机数(无用数据,仅作混淆使用)
 * 第2位是 加密的salt 1-9
 * 第3-12位 去除手机号的第一位1, 当前位的手机号数字+该数字在手机号的位数+salt进行计算,不进位保留个位数
 * 第13 随机数(无用数据,,仅作混淆使用)
 */

$phone = 13690258147;//随便输入一个号码

echo '原始手机号:' . $phone . "\n";
echo '加密数据:' . $enc = JustEncrypt::encrypt($phone);
echo "\n";
echo '解密结果:' . JustEncrypt::decrypt($enc);
die;

class JustEncrypt{

    public static function encrypt($phone){
        if (!preg_match('/^1[3-9]\d{9}$/', $phone)) {
            throw new InvalidArgumentException('手机号有误!');
        }
        $numArr = preg_split('//', $phone, -1, PREG_SPLIT_NO_EMPTY);
        array_shift($numArr);
        $result = [];
        $result[] = rand(1, 9); //第一位添加四季树
        $salt = rand(1, 9);//生成salt
        $result[] = $salt;
        foreach ($numArr as $key => $num) {
            $sum = $num + $key + 2 + $salt;
            $sum = substr($sum, -1, 1);
            $result[] = $sum;
        }
        $result[] = rand(0, 9);//结尾添加随机数

        return implode('', $result);
    }


    public static function decrypt($ciphertext){
        if (!preg_match('/^\d{13}$/', $ciphertext)) {
            throw new InvalidArgumentException('解密参数有误!');
        }
        $numArr = preg_split('//', $ciphertext, -1, PREG_SPLIT_NO_EMPTY);
        array_shift($numArr); //剔除第一位
        array_pop($numArr); //剔除最后一位
        $result[] = 1;//第一位手机号都是默认 1
        $salt = array_shift($numArr); //取出加密salt
        foreach ($numArr as $key => $vo) {
            $sum = 20 + $vo - $key - 2 - $salt;
            $sum = substr($sum, -1, 1);
            $result[] = $sum;
        }

        return implode('', $result);;
    }
}
