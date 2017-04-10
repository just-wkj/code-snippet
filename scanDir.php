<?php
//遍历路径文件
function read_all_dir($dir){
	$result = $cityArr = array();
	$handle = opendir($dir);
	if ($handle) {
		while (($file = readdir($handle)) !== false) {
			if ($file != '.' && $file != '..') {
				$cur_path = $dir . DIRECTORY_SEPARATOR . $file;
				if (is_dir($cur_path)) {
					$result['dir'][$cur_path] = read_all_dir($cur_path);
				} else {
					//读取配置文件中的城市和城市名称
					$currentFileContent = file_get_contents($cur_path);
					$mateched = preg_match('/define\s*\(\s*P_CITY_ENG\s*,\s*"(?P<city>.*?)"\s*\).*?define\s*\(\s*P_CITY_NAME\s*,\s*"(?P<city_name>.*?)"\s*\)/s', $currentFileContent, $out);
					if ($mateched) {
						$currentCity = $out['city'];
						$currentCityName = $out['city_name'];
						$cityArr[$currentCity] = $currentCityName;
					}
					$result['file'][] = $cur_path;
				}
			}
		}
		closedir($handle);
	}

	return array(
		$result,
		$cityArr
	);
}


$fileReturn = read_all_dir('../config');