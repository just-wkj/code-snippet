<?php
/**
 * @author     :wkj
 * @createTime :2017/10/25 9:34
 * @description:
 */

class Rent implements SplObserver{
    public function update(SplSubject $subject){
        // TODO: Implement update() method.
        echo __METHOD__;
        echo '租房处理';
        echo "\n";
    }
}