<?php
/**
 * @author     :wkj
 * @createTime :2017/10/25 9:27
 * @description:
 */

class User implements SplSubject{
    private $observers = array();
    private $name;

    public function __construct($name){
        $this->name = $name;
    }

    public function attach(SplObserver $observer){
        $this->observers[] = $observer;
        // TODO: Implement attach() method.
    }

    public function detach(SplObserver $observer){
        // TODO: Implement detach() method.
        $key = array_search($observer, $this->observers);
        if ($key !== false) {
            unset($this->observers[$key]);
        }
    }

    public function notify(){
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
        // TODO: Implement notify() method.
    }

    public function login(){
        echo 'login num ' . rand(1, 10);
    }

    public function getName(){
        return $this->name;
    }
}