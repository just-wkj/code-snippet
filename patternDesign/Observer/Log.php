<?php
/**
 * @author     :wkj
 * @createTime :2017/10/25 9:27
 * @description:
 */

class Log implements SplObserver{
    public function update(SplSubject $subject){
        // TODO: Implement update() method.
        echo __METHOD__;
        echo '登录日志处理';
        echo "\n";
    }
}