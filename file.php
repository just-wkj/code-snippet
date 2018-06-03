<?php

class RecursiveFileFilterIterator extends FilterIterator {
    // 满足条件的扩展名
    protected $ext = array('txt','csv');
    /**
     * 提供 $path 并生成对应的目录迭代器
     */
    public function __construct($path) {
        parent::__construct(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path)));
    }
    /**
     * 检查文件扩展名是否满足条件
     */
    public function accept() {
        $item = $this->getInnerIterator();
        if ($item->isFile() && 
                in_array(pathinfo($item->getFilename(), PATHINFO_EXTENSION), $this->ext)) {
            return TRUE;
        }
    }
}
// 实例化
foreach (new RecursiveFileFilterIterator('./') as $item) {
	echo $item . PHP_EOL;
}

// print_r(listDir('./'));

