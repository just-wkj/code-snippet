<?php
/**
 * @author     :wkj
 * @createTime :2017/10/24 8:55
 * @description:
 */

spl_autoload_register(function($class){
    include_once $class.'.php';
});



// 直接调用 需要知道class名 调用很多需要多处修改
$db = new PDOdb();
$db->conn();
$db->query();


//工厂调用 只需要知道接口传递参数,改动较小
$db = Factory::createDB('pdo');
$db->query();

echo "\n";
//抽象工厂 开放接口,便于拓展,不修改之前代码
$db = AFactory::createMysql();
$db->query();