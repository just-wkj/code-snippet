<body style='font-size: 15px;color: #00d900;background-color: #2b2b2b;'></body>
<?php
//php 日志文件快速查看
define('BASE_DIR', '../runtime/');
if(!is_dir(BASE_DIR)){
    echo '日志目录不存在! 请查看配置!';die;
}
if (!isset($_GET['file']) || $_GET['file'] === '') {
    $handler = opendir(BASE_DIR);
    $files = [];
    while (($filename = readdir($handler)) !== false) {//务必使用!==，防止目录下出现类似文件名“0”等情况
        if(!is_file($filename)){
            continue;
        }
        $currentFileData = explode('.', $filename);
        if (end($currentFileData) == 'log') {
            $files[] = $filename;
        }
    }
    closedir($handler);
    if ($files) {
        echo '<span style="color:white;text-decoration: underline;margin-left: 20px;line-height:40px">日志文件列表</span><br>';
        foreach ($files as $fileName) {
            echo '<a href="?file=' . $fileName . '" style="color:#00d900;text-decoration: underline;margin-left: 20px;line-height:20px">' . $fileName . '</a><br>';
        }
    }
    die;
}

$filename = $_GET['file'];
$filename = trim($filename, '/\\\\');
$filename = BASE_DIR.'/'.$filename;
$fp = fopen($filename, "r") or die("Couldn't open $filename");
?>
<div style="padding: 20px;">
    <a href="?" style="color:white;text-decoration: underline;margin-left: 20px">所有日志</a>
    <a href="?file=<?=$_GET['file']?>&sort=asc" style="color:white;text-decoration: underline;margin-left: 20px">时间正序↓</a>
    <a href="?file=<?=$_GET['file']?>&sort=desc" style="color:white;text-decoration: underline;margin-left: 20px">时间倒序↑</a>
    <br>
    <br>
    <?php
    $data = [];
    while (!feof($fp)) {
        $line = fgets($fp);
        $data[] = $line . '<br>';
    }
    if (isset($_GET['sort']) && $_GET['sort'] == 'desc') {
        $data = array_reverse($data);
    }

    foreach ($data as $line) {
        print $line;
    }
    ?>
</div>



