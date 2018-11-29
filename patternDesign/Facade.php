<?php
/**
 * 外观模式 降低系统复杂度
 *
 * @author     :wkj
 * @createTime :2018/11/29 9:25
 * @description:
 */

class FileReader{
    public function read($filename){
        try {
            return trim(file_get_contents($filename));
        } catch (Exception $exception) {
            throw new Exception('文件不存在!');
        }
    }
}

class Cipher{
    public function Encrypt($string){
        return md5($string);
    }
}

class Write{
    public function save($filename, $string){
        return file_put_contents($filename, $string);
    }
}

class EncryptFacade{
    private $fileReader;
    private $cipher;
    private $write;

    public function __construct(){
        $this->fileReader = new FileReader();
        $this->cipher = new Cipher();
        $this->write = new Write();
    }

    public function Encrypt($filename, $newfile){
        $string = $this->fileReader->read($filename);
        var_dump($string);
        $encrytpString = $this->cipher->Encrypt($string);
        $this->write->save($newfile, $encrytpString);
    }
}

//测试
(new EncryptFacade())->Encrypt('1.txt', 'encrypt.txt');
