<?php

if(!class_exists('Memcache')){
    class Memcache{
        public function __call($param,$value){
            return true;
        }
    }
}
if (!function_exists('p') ){
    function p(){
        echo "<pre>";
        foreach (func_get_args() as $variable) {
            if(is_array($variable)){
                echo 'array.length=' . count($variable);
                echo "\r\n";
                print_r($variable);
            } else {
                var_dump($variable);
            }
            echo "\r\n";
        }
        echo "</pre>\r\n";
        die;
    }
}

if( !function_exists('jlog') ){
	function justLog1($log,$type='sql'){
        if(is_array($log)){
            $log = json_encode($log);
        }
		$filename = 'C:/phpStudy/moyixi/'.date("Ymd").'_'.$type.".log";
		@$handle=fopen($filename, "a+");
		@fwrite($handle, date('Y-m-d H:i:s')."  ".$log."\r\n");
		@fclose($handle);
	}
}
if( !function_exists('fdump') ){
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
		$start = $func->getStartLine();
		$end =  $func->getEndLine();
		$filename = $func->getFileName();
		echo "function $funcname defined by $filename($start - $end)\n";
	}
}

if(!function_exists('justSplitx')){
    function justSplitx($str){
        $arr = preg_split('/[,\s]+/', $str, -1, 1);
        $arr = array_unique($arr);
        return $arr;
    }
}

if(!function_exists('justDiff')){
    function justDiff($str1, $str2, $reg='/[,\s]+/'){
        $justSplit = function ($str) use($reg){
            $arr = preg_split($reg, $str, -1, 1);
            $arr = array_unique($arr);
            return $arr;
        };

       $a = $justSplit($str1);
       $b = $justSplit($str2);
       $adiffb = array_diff($a, $b);
       $bdiffa = array_diff($b, $a);
       $ainterb=array_intersect($a,$b);
       $aandb= array_unique(array_merge($a, $b));
        echo "A ".implode(',', $a)."\n";
        echo "B ".implode(',', $b)."\n";
        echo "A-B ". implode(',', $adiffb)."\n";
        echo "B-A ". implode(',', $bdiffa)."\n";
        echo "A∩B ". implode(',', $ainterb)."\n";
        echo "A∪B ". implode(',', $aandb)."\n";
    }
}
