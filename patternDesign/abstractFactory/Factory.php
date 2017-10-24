<?php
/**
 * 工厂
 * @author     :wkj
 * @createTime :2017/10/24 9:13
 * @description:
 */

class Factory{
    public static function createDB($dbType){
        if ($dbType == 'mysql') {
            return new Mysql();
        } else if ($dbType == 'pdo') {
            return new PDOdb();
        } else {
            throw new Exception('db type not exists!');
        }
    }
}