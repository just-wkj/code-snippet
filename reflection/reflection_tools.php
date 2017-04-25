<?php
/**
 * @author     :wkj
 * @createTime :2017/4/25 9:53
 * @description:    反射小工具
 */

/**
 * 获取函数被定义的文件位置 打印函数代码
 * author    wkj
 * date 2017/4/25 10:10
 * @param      $funcname 函数名或者是类方法数组 数组第一值是类的实例或者类名第二个是方法名
 * @param bool $showSource 是否展示源码
 */

function fdump($funcname, $showSource = false){
	try {
		if (is_array($funcname)) {
			$func = new ReflectionMethod($funcname[0], $funcname[1]);
			$funcname = $funcname[1];
		} else {
			$func = new ReflectionFunction($funcname);
		}
	} catch (ReflectionException $e) {
		echo $e->getMessage();

		return;
	}
	$start = $func->getStartLine();
	$end = $func->getEndLine();
	$filename = $func->getFileName();
	echo "function $funcname defined by $filename($start - $end)\n";
	if ($showSource) {
		if (!function_exists('getLine')) {
			echo "函数getLine未定义";
		} else {
			echo "\n=========\n";
			echo getLine($filename, $func->getStartLine(), $func->getEndLine());
			echo "\n==========\n";
		}
	}
}

/**
 * 获取指定行内容
 * author    wkj
 * date 2017/4/25 10:10
 * @param      $file    文件路径
 * @param      $line    行数
 * @param null $endLine 结束函数
 * @param      $length  指定行返回内容长度
 * @return null|string
 */

function getLine($file, $line = null, $endLine = null, $length = null){
	$returnTxt = null;
	$i = 1;
	if (!$endLine) {
		$endLine = $line;
	}
	$handle = @fopen($file, "r");
	if ($handle) {
		while (!feof($handle)) {
			if ($length) {
				$buffer = fgets($handle, $length);
			} else {
				$buffer = fgets($handle);
			}
			if ($line) {
				if ($endLine >= $line && $i >= $line && $i <= $endLine) {
					$returnTxt .= $buffer;
				}
				if ($i > $endLine) {
					break;
				}
			} else {
				$returnTxt .= $buffer;
			}
			$i++;
		}
		fclose($handle);
	}

	return $returnTxt;
}

//使用方式

class A{
	public function test(){
		echo 111;
	}

}

$a = new A();
echo fdump(array(
	'A',
	'test'
), true);
echo fdump(array(
	$a,
	'test'
), true);
echo fdump('fdump', true);