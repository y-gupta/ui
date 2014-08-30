<?php
namespace ui\spintax;
function spin($str)
{
	do {
		$str = \ui\spintax\regex($str);
	} while (\ui\spintax\complete($str));
	return $str;  
}
function regex($str)
{
	if(preg_match("/{[^{}]+?}/", $str, $match)){
    	// Now spin the first captured string
    	$attack = explode("|", $match[0]);
    	$new_str = preg_replace("/[{}]/", "", $attack[rand(0,(count($attack)-1))]);
    	$str = str_replace($match[0], $new_str, $str);
    }
	return $str;
}

function complete($str)
{
	$complete = preg_match("/{[^{}]+?}/", $str, $match);
	return $complete;
}
