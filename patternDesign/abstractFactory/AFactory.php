<?php
/**
 * 抽象工厂
 * @author     :wkj
 * @createTime :2017/10/24 9:19
 * @description:
 */

class AFactory{
    public static function createMysql(){
        return new Mysql();
    }

    public static function createPDO(){
        return new PDOdb();
    }
}