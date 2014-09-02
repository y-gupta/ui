<?php
/**
 * This script parses the PATH_INFO and decides which script to be called, and what parameters to be passed
 * To falicitate use of one ui installation over multiple applications,
 * $_APP_DIR can be set in the app's index.php to dirname(__FILE__). See importer/index.php for an example.
 * $URL: https://subversion.assembla.com/svn/theappbin/ui/trunk/index.php $
 * @package ui\core
 * @author Yash Gupta <technofreak777@gmail.com>
 * @version $Id: index.php 44 2013-09-22 16:06:36Z technofreak $
 * @copyright 2013 by @author - All Rights Reserved
 * @license http://theappbin.com/licence/ui Alternatively see LICENCE file included in the root directory of this script
 */
/** FRAMEWORKED is defined as true for possible use in application to avoid direct access. */
define('FRAMEWORKED',true);
/** FROM_CLI is true when the PHP script is called from commandline */
define('FROM_CLI',!isset($_SERVER['REMOTE_ADDR']));
if(FROM_CLI)/** NO_TEMPLATE is needed on CLI */
    define('NO_TEMPLATE',true);
include(dirname(__FILE__).'/core/ui.php');
/** 
 * When accessing from CLI, PATH_INFO is not supported. So, take path insformation as the 1st argument to this script
 * @example php /path/to/index.php /help/contact?get_var=value&more_stuff 
 */
if(FROM_CLI){
    if(isset($argv[1])){
        $query_string='';$tmp=strpos($argv[1],'?');
        if($tmp!==false)
            $query_string=substr($argv[1],$tmp+1);
        $_SERVER['QUERY_STRING']=$query_string;
        unset($query_string);
        parse_str($_SERVER['QUERY_STRING'],$_GET);
        $params=trim($argv[1],"/\\");
    }else $params='';
}else{
  if(isset($_SERVER['PATH_INFO']))
    $params=trim($_SERVER['PATH_INFO'],"/\\");
  elseif(isset($_SERVER['ORIG_PATH_INFO'])){
    $params=trim($_SERVER['ORIG_PATH_INFO'],"/\\");
  }
  elseif(defined('APP_BASE_URL')){
/**
 * APP_BASE_URL must be defined in somewhere (preferably constants.php)
 * in case you expeience problems with a url_rewite to index.php/[path]
 * IT must start with a '/', be relative to your domain name. Case insensitive (only strlen is used). Eg.
 * If your application's indx.php is http://a.b/c/d/index.php
 * then APP_BASE_URL is /c/d
 */
    $params=substr($_SERVER['REQUEST_URI'],strlen(APP_BASE_URL)).'?';
    $params=substr($params,0,strpos($params,'?'));
    $params=trim($params,"/\\");
    unset($_GET['UI_PATH_INFO']);
  }else
    $params='';
}
\ui\global_var('path',$params,true);
if($params=='')
  $params=array();
else
  $params=explode('/',$params);
$ui_controller='';
$n_params=count($params);
$is_dir=true;
/**
 * PATH_INFO is parsed incrementally from the lowest level, and goes till the next higher level is an invalid controller.
 * When the deepest possible controller is found, the subsequent parameters are filled in the $_PARAM global array
 * To support directories as controllers, index.php of that directory is called when directory is tried to be accessed.
 * For example, when `[base_url]parent_dir/called_dir/the_parameter.php` is called, 
 * `[base_path]app/parent_dir/called_dir/the_parameter.php` is called if it exists with empty $_PARAM, otherwise
 * `[base_path]app/parent_dir/called_dir/index.php` is called if it exists with `the_parameter` in $_PARAM, otherwise
 * `[base_path]app/parent_dir/called_dir.php` is called if it exists with `the_parameter` in $_PARAM, otherwise
 * `[base_path]app/parent_dir/index.php` is called if it exists with `called_dir` and `the_parameter` in $_PARAM, otherwise
 * `[base_path]app/parent_dir.php` is called if it exists with `called_dir` and `the_parameter` in $_PARAM, otherwise
 * `[base_path]app/index.php` is called if it exists with `parent_dir`, `called_dir` and `the_parameter` in $_PARAM, otherwise
 * a 404 error is triggered, and another error is triggered requiring presence of index.php
 * Also, the `[base_path]app/parent_dir/called_dir/index.php` can be called (if exists)
 * by using `[base_url]parent_dir/called_dir/index`
 */
for($i=0;$i<$n_params;$i++)
{
/**
 * PHP files/directories with name starting with a _ (underscore) are not allowed to be called via. the Router
 * When such a request is made, the parent controller applicable is called with the requested path/file in $_PARAM.
 * Note that a directory of type _dir restricts access to everything under it,
 * and the components of remaining path are passed in $_PARAM
 * Direct access is still possible if not blocked by .htaccess or your own mechanisms (like checking the constant FRAMEWORKED)
 */
  if(isset($params[$i][0])&&$params[$i][0]==='_')
    break;
  $proposed=$_APP_DIR.'app/'.($i===0?$params[$i]:$ui_controller.'/'.$params[$i]);
  if(is_dir($proposed)&&file_exists($proposed.'/index.php'))
  {
    $is_dir=true;
    $ui_controller.=($i===0?'':'/').$params[$i];
  }elseif(file_exists($proposed.'.php'))
  {
    $is_dir=false;
    $ui_controller.=($i===0?'':'/').$params[$i];
  }
  else break;
}

$_PARAM=array_slice($params,$i);
$_ENV['param']=$_ENV['PARAM']=$_ENV['_PARAM']=$_ENV['_param']=&$_PARAM;

if($is_dir===true)
  $ui_controller.=$i===0?'index':'/index';

\ui\global_var('controller',$ui_controller,true);
$ui_filepath=$_APP_DIR.'app/'.$ui_controller.'.php';
include(dirname(__FILE__).'/core/init.php');
if($i===0&&!file_exists($ui_filepath)){
  include($_APP_DIR.'/inc/404.php');
  trigger_error('UI: '.$_APP_DIR.'app/index.php is required for handeling a completely non-existant path',E_USER_ERROR);
  exit();
}
/**
 * Clean up all variables no longer required for minimum footprint
 */
unset($ui_controller);unset($is_dir);unset($_APP_DIR);unset($params);unset($i);unset($n_params);unset($proposed);
include($ui_filepath);