<?php
require('./PHPMailer/class.phpmailer.php');

$mail = new PHPMailer(); //实例化

$mail->IsSMTP(); // 启用SMTP

$mail->Host = "smtp.163.com"; //SMTP服务器 163邮箱例子
//$mail->Host = "smtp.126.com"; //SMTP服务器 126邮箱例子
//$mail->Host = "smtp.qq.com"; //SMTP服务器 qq邮箱例子

$mail->Port = 25;  //邮件发送端口
$mail->SMTPAuth   = true;  //启用SMTP认证

$mail->CharSet  = "UTF-8"; //字符集
$mail->Encoding = "base64"; //编码方式

$mail->Username = "justwkj@163.com";  //你的邮箱
$mail->Password = "xxx";  //你的密码
$mail->Subject = "xxx你好"; //邮件标题

$mail->From = "justwkj@163.com";  //发件人地址（也就是你的邮箱）
$mail->FromName = "justwkj";   //发件人姓名

$address = "602823863@qq.com";//收件人email
$mail->AddAddress($address, "xxx1");    //添加收件人1（地址，昵称）
//$mail->AddAddress($address2, "xxx2");    //添加收件人2（地址，昵称）

$mail->AddAttachment('./xxmail.zip','邮件发送类.zip'); // 添加附件,并指定名称
$mail->AddAttachment('img.jpg'); // 可以添加多个附件

$mail->IsHTML(true); //支持html格式内容
//$mail->AddEmbeddedImage("logo.jpg", "my-attach", "logo.jpg"); //设置邮件中的图片
$mail->Body = '你好, <b>朋友</b>! <br/>这是一封邮件！'; //邮件主体内容

//发送
if(!$mail->Send()) {
  echo "发送失败: " . $mail->ErrorInfo;
} else {
  echo "成功";
}
?>


