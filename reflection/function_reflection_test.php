<?php
/**
 * @author     :wkj
 * @createTime :2017/4/25 8:57
 * @description: 测试函数反射
 */


/**
测试函数
 */
function test($params1,$params2= 2){
	echo $params1."\n";
	echo __FUNCTION__;
}

$ref_f = new ReflectionFunction('test');
echo $ref_f->getFileName(),"\n";
$r = $ref_f->getParameters();
print_r($r);
echo "\n";
echo $ref_f->getStartLine().'=>'.$ref_f->getEndLine();
echo "\n";
echo $ref_f->getDocComment();
echo "\n";
echo $ref_f->getNumberOfParameters();
echo "\n";
echo $ref_f->getNumberOfRequiredParameters();

$s = $ref_f->getStartLine();
$e = $ref_f->getEndLine();

class A{
	public function show(){
		echo 111;
	}
}
$a = new A();
fdump(array($a,'show'));
function fdump($funcname) {
	try {
		if(is_array($funcname)) {
			$func = new ReflectionMethod($funcname[0], $funcname[1]);
			$funcname = $funcname[1];
		} else {
			$func = new ReflectionFunction($funcname);
		}
	} catch (ReflectionException $e) {
		echo $e->getMessage();
		return;
	}
	$start = $func->getStartLine() - 1;
	$end =  $func->getEndLine() - 1;
	$filename = $func->getFileName();
	echo "function $funcname defined by $filename($start - $end)\n";
}

echo "\n";
$rs = $ref_f->getClosure();
print_r($rs);

echo "\n====================\n";

function test1() {
	return 100;
};

function testClosure(Closure $callback)
{
	return $callback();
}

$callback = function(){
	echo 12321;
};
$a = testClosure($callback);
print_r($a);


/**
 * 获取指定行内容
 * author	wkj
 * date 2017/4/25 9:53
 * @param      $file   文件路径
 * @param      $line   行数
 * @param null $endLine 结束函数
 * @param      $length 指定行返回内容长度
 * @return null|string
 */

function getLine($file, $line=null, $endLine=null,$length = null){
	$returnTxt = null;
	$i = 1;
	if(!$endLine){
		$endLine = $line;
	}
	$handle = @fopen($file, "r");
	if ($handle) {
		while (!feof($handle)) {
			if($length){
				$buffer = fgets($handle, $length);
			} else {
				$buffer = fgets($handle);
			}
			if($line){
				if($endLine >= $line && $i>=$line && $i <= $endLine){
					$returnTxt .= $buffer;
				}
				if($i>$endLine){
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
echo "\n";
echo "函数起始位置是".$s.",函数结束位置是$e";
echo "\n";
$rs = getLine('./function_reflection_test.php');
echo $rs;