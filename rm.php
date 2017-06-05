<?php
//删除文件和目录
$dir = 'D:\gitrepo\mazntest\Application\Runtime';
//循环删除目录和文件函数
unlinkRecursive($dir, 0);
function unlinkRecursive($dir, $deleteRootToo){
    if (!$dh = @opendir($dir)) {
        return;
    }
    while (false !== ($obj = readdir($dh))) {
        if ($obj == '.' || $obj == '..') {
            continue;
        }
        if (!@unlink($dir . '/' . $obj)) {
            unlinkRecursive($dir . '/' . $obj, true);
        }
    }
    closedir($dh);
    if ($deleteRootToo) {
        @rmdir($dir);
    }

    return;
}
