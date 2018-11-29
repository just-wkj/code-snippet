<?php
/**
 * @author     :wkj
 * @createTime :2018/11/28 9:08
 * @description:
 */

class Observal implements SplSubject{
    private $contains = [];
    public $rand = '';

    /**
     * Attach an SplObserver
     * @link  http://php.net/manual/en/splsubject.attach.php
     * @param SplObserver $observer <p>
     *                              The <b>SplObserver</b> to attach.
     *                              </p>
     * @return void
     * @since 5.1.0
     */
    public function attach(SplObserver $observer){
        // TODO: Implement attach() method.
        if(!in_array($observer, $this->contains)){
            $this->contains[] = $observer;
        }
    }

    /**
     * Detach an observer
     * @link  http://php.net/manual/en/splsubject.detach.php
     * @param SplObserver $observer <p>
     *                              The <b>SplObserver</b> to detach.
     *                              </p>
     * @return void
     * @since 5.1.0
     */
    public function detach(SplObserver $observer){
        // TODO: Implement detach() method.
        if(in_array($observer, $this->contains)){
            unset($this->contains[$observer]);
        }
    }

    /**
     * Notify an observer
     * @link  http://php.net/manual/en/splsubject.notify.php
     * @return void
     * @since 5.1.0
     */
    public function notify(){
        foreach ($this->contains as $watcher){
            $watcher->update($this);
        }
        // TODO: Implement notify() method.
    }
    public function rand(){
        $this->rand = rand(1,100);
        $this->notify();
    }
}

// watcher
class Watcher1 implements SplObserver{
    /**
     * Receive update from subject
     * @link  http://php.net/manual/en/splobserver.update.php
     * @param SplSubject $subject <p>
     *                            The <b>SplSubject</b> notifying the observer of an update.
     *                            </p>
     * @return void
     * @since 5.1.0
     */
    public function update(SplSubject $subject){
        // TODO: Implement update() method.
        echo   '通知'.__CLASS__.PHP_EOL;
        echo $subject->rand;
    }
}

class Watcher2 implements SplObserver{
    /**
     * Receive update from subject
     * @link  http://php.net/manual/en/splobserver.update.php
     * @param SplSubject $subject <p>
     *                            The <b>SplSubject</b> notifying the observer of an update.
     *                            </p>
     * @return void
     * @since 5.1.0
     */
    public function update(SplSubject $subject){
        // TODO: Implement update() method.
        echo   '通知'.__CLASS__.PHP_EOL;
        echo $subject->rand;
    }
}

$subject = new Observal();
$watcher1 = new Watcher1();
$watcher2 = new Watcher2();
$subject->attach($watcher1);
$subject->attach($watcher2);
//$subject->notify();
$subject->rand();
