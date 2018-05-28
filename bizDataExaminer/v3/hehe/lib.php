<?php

function ltredc($str){
    $table = str_split('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ@~!*()-_.\'');
    $rawList = array();
    $offsetList = array();
    foreach (array_reverse(str_split($str)) as $k => $v) {
        $pos = array_search($v, $table);
        if (intval(($k + 1) % 2) == 1) {
            $offsetList[] = $pos;
        } else {
            $rawPos = intval($pos - $offsetList[($k + 1) / 2 - 1]);
            @$rawList[] = $table[$rawPos];
        }
    }
    $raw = str_replace('@', '%', implode('', $rawList));
    return urldecode($raw);
}


function __mkdirs($dir, $mode = 0777)
{
	if (!is_dir($dir)) {
		__mkdirs(dirname($dir), $mode);
		return @mkdir($dir, $mode);
	}
	return true;
}


function __rmdirs($dir) {
	if (is_dir($dir)) {
		$objects = scandir($dir);
		foreach ($objects as $object) {
			if ($object != "." && $object != "..") {
			if (filetype($dir.DIRECTORY_SEPARATOR.$object) == "dir") __rmdirs($dir.DIRECTORY_SEPARATOR.$object); else unlink($dir.DIRECTORY_SEPARATOR.$object);
			}
		}
		reset($objects);
		rmdir($dir);
	}
}