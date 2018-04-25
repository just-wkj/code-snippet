<?php
/**
 * @author     :wkj
 * @createTime :2018/4/3 17:03
 * @description:
 */

abstract class AbstractFactory
{
    abstract public function createText(string $content): Text;
}