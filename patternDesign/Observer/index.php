<?php
/**
 * @author     :wkj
 * @createTime :2017/10/25 8:50
 * @description:
 */


spl_autoload_register(function($class){
    include_once $class.'.php';
});
$user = new User('xx');
$user->attach(new Log());
$user->attach(new Rent());
$user->login();
//$user->detach(new Log());
$user->notify();



