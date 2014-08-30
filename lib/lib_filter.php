<?php
/**
 * This library filters data (from user land).
 * $URL: https://subversion.assembla.com/svn/theappbin/ui/trunk/lib/lib_filter.php $
 * @package ui\libraries\filter
 * @author Yash Gupta <technofreak777@gmail.com>
 * @version $Id: lib_filter.php 42 2013-08-07 13:48:29Z technofreak $
 * @copyright 2013 by @author - All Rights Reserved
 * @license http://theappbin.com/licence/ui Alternatively see LICENCE file included in the root directory of this script
 */
namespace ui\filter;
function &for_echo(&$in,$allowed=false)
{
	if(!is_string($in)){
		if(is_array($in))
		{
			foreach($in as &$inner){
				for_echo($inner,$allowed);
			}
		}
		return $in;
	}
    if (get_magic_quotes_gpc())
        $in = stripslashes($in);                                  
	if($allowed!==false)
		$in=strip_tags($in,$allowed);
	$in = htmlentities($in);
	return $in;
}
/**
 * Filters input arting|array of strings for HTML attrs or <textareas>
 * Note that HTML tags will not be parsed as it converts <,>, etc.
 * @param bool|string $allowed if false, HTML tags are not stripped. Otherwise $allowed HTML tags are passed to strip_tags
 */
function &for_html(&$in,$allowed=false)
{
  if(!is_string($in)){
		if(is_array($in))
		{
			foreach($in as &$inner){
				for_html($inner,$allowed);
			}
		}
		return $in;
	}
  if($allowed!==false)
    $in=strip_tags($in,$allowed);
  $in=htmlspecialchars($in,ENT_QUOTES,'UTF-8');
  return $in;
}
function &for_db(&$in,$allowed=false,$escape=true)
{
	if(!is_string($in)){
		if(is_array($in))
		{
			foreach($in as &$inner){
				for_db($inner,$allowed,$escape);
			}
		}
		return $in;
	}
	if($allowed!==false)
		$in=strip_tags($in,$allowed);
	if(get_magic_quotes_gpc())
    $in=stripslashes($in);
	if($escape){
	 \ui\load_lib('db');
    $in=mysqli_real_escape_string(\ui\db\db(),$in);
  }
  return $in;
}