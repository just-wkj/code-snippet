<?php
//正则表达式校验工具 add by wkj 2017/04/9
function preg_regex_to_pattern($raw_regex, $modifiers = "") {
	if (!preg_match('{\\\\(?:/;$)}', $raw_regex)) {
		$cooked = preg_replace('!/!', '\/', $raw_regex);
	} else {
		$pattern = '{ [^\\\\/]+ |\\\\. |( / |\\\\$ ) }sx';
		$f = create_function('$matches', '
										if (empty($matches[1])){
										return $matches[0];
										} else {
										return "\\\\" . $matches[1];
										}');
		$cooked = preg_replace_callback($pattern, $f, $raw_regex);
	}

	return "/$cooked/$modifiers";
}


function preg_pattern_error($pattern) {
	if ($old_track = ini_get("track_errors")) {
		$old_message = isset($php_errormsg) ? $php_errormsg : false;
	} else {
		ini_set('track_errors', 1);
	}
	unset($php_errormsg);
	@ preg_match($pattern, "");
	$return_value = isset($php_errormsg) ? $php_errormsg : false;

	if ($old_track) {
		$return_value = isset($old_message) ? $old_message : false;
	} else {
		ini_set('track_errors', 0);
	}

	return $return_value;
}

function preg_regex_error($regex) {
	return preg_pattern_error(preg_regex_to_pattern($regex));
}