<?php
/**
 * @author     :wkj
 * @createTime :2017/10/27 9:08
 * @description: 开闭原则 对拓展开放 对修改关闭
 */

/*代码实现违反了“开-闭”原则，一个软件实体应当对扩展开放，对修改关闭。即在设计一个模块的时候，应当使这个模块可以在不被修改的前提下被扩展。

重新设计代码，使用适配器模式来实现。

适配器模式核心思想：把对某些相似的类的操作转化为一个统一的“接口”(这里是比喻的说话)--适配器，或者比喻为一个“界面”，统一或屏蔽了那些类的细节。
适配器模式还构造了一种“机制”，使“适配”的类可以很容易的增减，而不用修改与适配器交互的代码，符合“减少代码间耦合”的设计原则。

代码 引用博客地址 http://www.cnblogs.com/whoknows/articles/adapter_in_php.html
 * */

abstract class Toy{
    public abstract function openMouth();

    public abstract function closeMouth();
}

class Dog extends Toy{
    public function openMouth(){
        echo "Dog open Mouth\n";
    }

    public function closeMouth(){
        echo "Dog close Mouth\n";
    }
}

class Cat extends Toy{
    public function openMouth(){
        echo "Cat open Mouth\n";
    }

    public function closeMouth(){
        echo "Cat close Mouth\n";
    }
}

echo "normal\n";
(new Dog())->closeMouth();
(new Cat())->openMouth();
echo "\n";
//正常
//目标角色:红枣遥控公司
interface RedTarget{
    public function doMouthOpen();

    public function doMouthClose();
}

interface GreenTarget{
    public function handleMouse($type = 0);
}

class RedAdapter implements RedTarget{
    private $adapter;

    public function __construct(Toy $adapter){
        $this->adapter = $adapter;
    }

    public function doMouthOpen(){
        echo 'red do mouth open' . "\n";
        $this->adapter->openMouth();
    }

    public function doMouthClose(){
        echo 'red do mouth close' . "\n";
        $this->adapter->closeMouth();
    }
}

class GreenAdapter implements GreenTarget{
    private $adapter;

    public function __construct(Toy $adapter){
        $this->adapter = $adapter;
    }

    public function handleMouse($type = 0){
        if ($type == 0) {
            $this->adapter->openMouth();
        } else {
            $this->adapter->closeMouth();
        }
    }

}

echo "增加\n";
(new RedAdapter(new Dog()))->doMouthClose();
(new GreenAdapter(new Cat()))->handleMouse(1);

