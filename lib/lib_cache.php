<?php
/**
 * This library handles caching to filesystem. Supports nesting, automaic identifier generation, file lock safety, etc.
 * $URL$
 * @package ui\libraries\cache
 * @author Yash Gupta <technofreak777@gmail.com>
 * @version $Id$
 * @copyright 2013 by @author - All Rights Reserved
 * @license http://theappbin.com/licence/ui Alternatively see LICENCE file included in the root directory of this script
 */
namespace ui\cache;
$ui_cache_ids=array();
$ui_cache_ignores=array();
/**
 * Makes an identifier for a cache block using various parameters
 * @return string md5() hash
 */
function make_id($custom='none',$ignore_param=false,$ignore_get=false,$ignore_post=false)
{
  $params=$custom;
  $params.=\ui\global_var('controller');
  if(!$ignore_param)
    $params.=\ui\global_var('path');
  if(!$ignore_get)
    $params.=serialize($_GET);
  if(!$ignore_post)
    $params.=serialize($_POST);
  return md5($params);
}
/**
 * Checks if a cache exists for the given identifer
 * @param string|bool $id specifies the identitifer of the cache. if false is given, an identifier is generated using make_id()
 * @param int|bool $timeout specifies the relative lifetime of the cache. if false is give, default lifetime from config.php is used.
 * @return bool|string if $return_data is true and cache exists, the data is returned. If $return_data is false and cache exists, boolean TRUE is returned. If cache is absent, boolean FALSE is returned 
 */ 
function exists($id=FALSE,$timeout=FALSE,$return_data=FALSE)
{
  if($timeout===FALSE)
    $timeout=\ui\config('cache_timeout',3600);
  if($id===FALSE)
    $id=make_id();
  $fname=\ui\config('cache_path').$id.\ui\config('cache_file_suffix');
  $fs=@fopen($fname,'rb',FASLE);
  if($fs===FALSE)
    return false;
  flock($fs,LOCK_SH);
  $time=fread($fs,4);
  if(!isset($time[3])){
    flock($fs,LOCK_UN);
    @unlink($fname);//Dele
    return FALSE;
  }
  $time=unpack('N',$time);
  $time=array_pop($time);
  if(time()-$time>=$timeout){
    flock($fs,LOCK_UN);
    @unlink($fname);//Dele
    return FALSE;
  }
  if($return_data)
  {
    $data='';
    while(!feof($fs))
      $data.=fread($fs,1024*512);//read in 512 KB blocks
    flock($fs,LOCK_UN);
    fclose($fs);
    return $data; 
  }
  flock($fs,LOCK_UN);
  fclose($fs);
  return TRUE;
}
/**
 * Starts a cached block. Nested cache blocks are allowed.
 * If cache is not found, boolean TRUE is returned.
 * If cache is found boolean FALSE, or the cached data is returned depending upon $return
 * @example if(\ui\cache\start()===TRUE){ [... expensive work...] \ui\cache\stop();}
 * @param $id string|bool If absent/false, then id is generated automatically using make_id()
 * @param $return bool wether to return cached data and not echo it, or echo it.
 */
function start($id=false,$timeout=false,$return=false)
{
  if($id===false)
    $id=make_id();
  if(isset($_GET['nocache'])&&$_GET['nocache']==\ui\config('nocache_code'))
  {
    global $ui_cache_ignores,$ui_cache_ids;
    $ui_cache_ids[]=$ui_cache_ignores[]=$id;
    return TRUE;
  }
  if( ($data=exists($id,$timeout,true)) !==false)
  {
    if($return)
      return $data;
    echo $data;
    return FALSE;
  }
  ob_start();
  global $ui_cache_ids;
  $ui_cache_ids[]=$id;
  \ui\register_hook('content_end','\\ui\\cache\\silent_stop');
  return TRUE;
}
/**
 * Bare bones, faster start()
 */
function start2($id=false,$timeout=false)
{
  if($id===false)
    $id=make_id();
  if(isset($_GET['nocache'])&&$_GET['nocache']==\ui\config('nocache_code'))
  {
    global $ui_cache_ignores,$ui_cache_ids;
    $ui_cache_ids[]=$ui_cache_ignores[]=$id;
    return TRUE;
  }
  if($timeout===FALSE)
    $timeout=\ui\config('cache_timeout',3600);
  $fname=\ui\config('cache_path').$id.\ui\config('cache_file_suffix');
  $fs=@fopen($fname,'rb',FASLE);
  if($fs!==FALSE)
  {
    flock($fs,LOCK_SH|LOCK_NB);
    $time=fread($fs,4);
    if(!isset($time[3])){
      fclose($fs);
      unlink($fname);
    }else{
      $time=unpack('N',$time);
      $time=array_pop($time);
      if(time()-$time<$timeout){
        while(!feof($fs))
          echo fread($fs,1024*8);//read in 8 KB blocks
        flock($fs,LOCK_UN|LOCK_NB);
        fclose($fs);
        return FALSE;
      }
      fclose($fs);
      unlink($fname);
    }
  }
  ob_start();
  global $ui_cache_ids;
  $ui_cache_ids[]=$id;
  return TRUE;
}
/**
 * Stops a cache block, and writes it's cache to file
 * Echoes/Returns the cached data depending upon $return
 * @return bool|string if $return is TRUE, the data is returned. If $return is FALSE, then TRUE is returned if cached was written successfully, or FALSE if it was not.
 */
function stop($return=FALSE)
{
  global $ui_cache_ids,$ui_cache_ignores;
  $id=array_pop($ui_cache_ids);
  if($ui_cache_ignores&&in_array($id,$ui_cache_ignores))
    return FALSE;
  if($id===NULL){
    if(DEBUG){
      echo PHP_EOL,'<div class="alert alert-error">',PHP_EOL,'ERROR \\ui\\cache\\stop(): Unmatched stop',PHP_EOL,'</div>',PHP_EOL;
    }
    return FALSE;
  }
  $data=ob_get_clean();
  $fname=\ui\config('cache_path').$id.\ui\config('cache_file_suffix');
  $fs=@fopen($fname,'wb',FALSE);
  if($fs!==FALSE)
  {
    flock($fs,LOCK_EX);
    fwrite($fs,pack('N',time()));
    fwrite($fs,$data);
    flock($fs,LOCK_UN);
    fclose($fs);
    if($return)
      return $data;
    echo $data;
    return TRUE;
  }
  if($return)
    return $data;
  echo $data;
  return FALSE;
}

/**
 * Silently stops. Does not show error if unmatched.
 */
function silent_stop()
{
  global $ui_cache_ids,$ui_cache_ignores;
  $id=array_pop($ui_cache_ids);
  if($ui_cache_ignores&&in_array($id,$ui_cache_ignores))
    return FALSE;
  if($id===NULL){
    return FALSE;
  }
  $data=ob_get_clean();
  $fname=\ui\config('cache_path').$id.\ui\config('cache_file_suffix');
  $fs=@fopen($fname,'wb',FALSE);
  if($fs!==FALSE)
  {
    flock($fs,LOCK_EX);//Lock the file for safety
    fwrite($fs,pack('N',time()));
    fwrite($fs,$data);
    flock($fs,LOCK_UN);
    fclose($fs);
    echo $data;
    return TRUE;
  }echo $data;
  return FALSE;
}

/**
 * Cache data, instead of text output
 * @param $var is filled with the data if cache is found. otherwise it is set to NULL
 * @return bool boolean false if cache exists, otherwise the ID of the cache (to use with data_stop)
 */
function data(&$var,$id=false,$timeout=false)
{
  if($id===false)
    $id=make_id();
  if(isset($_GET['nocache'])&&$_GET['nocache']==\ui\config('nocache_code'))
  {
    return FALSE;
  }
  if($timeout===FALSE)
    $timeout=\ui\config('cache_timeout',3600);
  $fname=\ui\config('cache_path').$id.\ui\config('cache_file_suffix');
  $fs=@fopen($fname,'rb',FALSE);
  if($fs!==FALSE)
  {
    flock($fs,LOCK_SH|LOCK_NB);
    $time=fread($fs,4);
    if(!isset($time[3])){
      fclose($fs);
      unlink($fname);
    }else{
      $time=unpack('N',$time);
      $time=array_pop($time);
      if(time()-$time<$timeout){
        $data='';
        while(!feof($fs))
          $data.=fread($fs,1024*1024);//read in 1 MB blocks
        flock($fs,LOCK_UN|LOCK_NB);
        fclose($fs);
        $var=unserialize($data);
        return FALSE;
      }
      fclose($fs);
      unlink($fname);
    }
  }
  return $id;
}

function data_stop(&$var,$id){
  $fname=\ui\config('cache_path').$id.\ui\config('cache_file_suffix');
  $fs=@fopen($fname,'wb',FALSE);
  if($fs!==FALSE)
  {
    flock($fs,LOCK_EX);//Lock the file for safety
    fwrite($fs,pack('N',time()));
    fwrite($fs,serialize($var));
    flock($fs,LOCK_UN);
    fclose($fs);
    return TRUE;
  }
  return FALSE; 
}