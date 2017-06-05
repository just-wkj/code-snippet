<?php
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
	$osArr = array();
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
			$osArr[] = $buffer;
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

	return $osArr;
	return $returnTxt;
}

function OSWrite($file,$data){
	$handle = @fopen($file, "w");
	if ($handle) {
		foreach ($data as $vo){
			fwrite($handle, $vo);
		}
		fclose($handle);
	}
}
define('OS_EOL', "\n");
$file = 'C:\Windows\System32\drivers\etc\hosts';
$hosts = getLine($file);

if (isset($_POST['sel'])){
	foreach($_POST['sel'] as $k => $v){
		$match = preg_match('/^.*?(\d+\.\d+\.\d+\.\d+\s+.*?)$/',$hosts[$k],$out);
		if($match){
			$hosts[$k] = $out[1] ;
		}
	}

	foreach ($hosts as $__k => $__v){
		if (array_key_exists($__k ,$_POST['sel'])) {
			$match = preg_match('/^.*?(\d+\.\d+\.\d+\.\d+\s+ .*?)$/',$__v,$out);
			if($match){
				$hosts[$__k] = $out[1] ;
			}
		} else {
			if (intval($__v)){
				$hosts[$__k] = '#'.$__v;
			} else if (preg_match('/^\s*$/', $__v)){
				$hosts[$__k] = "";
			}
		}
		$hosts[$__k] = trim($hosts[$__k],OS_EOL);
		$hosts[$__k] .= OS_EOL;
	}

	OSWrite($file, $hosts);
	header('location:.');die;
}


echo "<form  name='form' id='form' method='post'>";
foreach ($hosts as $key => $host) {
	$checked = intval($host) ? ' checked ' : '';
	echo "<input type='checkbox' name='sel[$key]' id='id_{$key}' $checked><label for='id_{$key}'>".$host."</label><br>";
}
echo "<input type='submit' name='xx' value='xx'>";
echo "</form>";

$xxx = <<<html
<script src="https://cdn.bootcss.com/jquery/1.12.4/jquery.min.js"></script>
<script type="text/javascript" language=JavaScript charset="UTF-8">
      document.onkeydown=function(event){
            var e = event || window.event || arguments.callee.caller.arguments[0];
             if(e && e.keyCode==13){ //enter
				$('#form').submit();
            }
        };
</script>
html;

echo $xxx;