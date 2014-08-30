<?php
/**
 * This library handles database (MySQLi)
 * $URL: https://subversion.assembla.com/svn/theappbin/ui/trunk/lib/lib_db.php $
 * @package ui\libraries\db
 * @author Yash Gupta <technofreak777@gmail.com>
 * @version $Id: lib_db.php 44 2013-09-22 16:06:36Z technofreak $
 * @copyright 2013 by @author - All Rights Reserved
 * @license http://theappbin.com/licence/ui Alternatively see LICENCE file included in the root directory of this script
 */
namespace ui\db;
$ui_db_res=$ui_db_db=null;
function db(){
  global $ui_db_db;
  if(!$ui_db_db)
  {
    $host=\ui\config('sql_host',NULL);
    if($host===NULL)
      $host=ini_get("mysqli.default_host");
      
    $user=\ui\config('sql_user',NULL);
    if($user===NULL)
      $user=ini_get("mysqli.default_user");
    
    $pass=\ui\config('sql_pass',NULL);
    if($pass===NULL)
      $pass=ini_get("mysqli.default_pw");
    
    $db=\ui\config('sql_db','');
    
    $port=\ui\config('sql_port',NULL);
    if($port===NULL)
      $port=ini_get("mysqli.default_port");
    
    $socket=\ui\config('sql_socket',NULL);
    if($socket===NULL)
      $socket=ini_get("mysqli.default_socket");

    $ui_db_db=mysqli_connect($host,$user,$pass,$db,$port,$socket);

    if(DEBUG&&mysqli_connect_error())
      echo '<div class="alert alert-error">'.PHP_EOL.'ERROR \\ui\\db\\db() => '.mysqli_connect_error().PHP_EOL.'</div>';
  }
  return $ui_db_db;
}
function escape($val)
{
  return mysqli_real_escape_string(db(),$val);
}
function ping()
{
  if(!mysqli_ping(db()))
  {
    global $ui_db_db;
    $ui_db_db=NULL;
    db();
  }
}
function query($q)
{
    global $ui_db_res;
    $ui_db_res=mysqli_query(db(),$q);
    if($ui_db_res===FALSE&&DEBUG)
    {
      echo '<div class="alert alert-error">'.PHP_EOL.'ERROR \\ui\\db\\query('.$q.') => '.error().PHP_EOL.'</div>';
    }
    return $ui_db_res;
}
function affected()
{
  return mysqli_affected_rows(db());
}
function count($res=null)
{
  global $ui_db_res;
  if($res===null)
      $res=$ui_db_res;
  if(!$res)
      return false;
  return mysqli_num_rows($res);
}
function error()
{
  return mysqli_error(db());
}
function assoc($res=null)
{
    global $ui_db_res;
    if($res===null)
        $res=$ui_db_res;
    if(!$res)
        return false;
    return mysqli_fetch_assoc($res);
}
function row($res=null)
{
    global $ui_db_res;
    if($res===null)
        $res=$ui_db_res;
    if(!$res)
        return false;
    return mysqli_fetch_row($res);
}
function insert($table,$data)
{
    $q='INSERT INTO `'.\ui\config('sql_prefix').$table.'` ';
    $fields='(';
    $vals=') VALUES (';
    $first=true;
    foreach($data as $key=>$val)
    {
        $vals.=($first===true?'':', ')."'".mysqli_real_escape_string(db(),$val)."'";
        $fields.=($first===true?'':', ')."`".$key."`";
        $first=false;
    }
    $q.=$fields.$vals;
    $q.=')';
    return \ui\db\query($q);
}
function replace($table,$data)
{
    $q='REPLACE INTO `'.\ui\config('sql_prefix').$table.'` ';
    $fields='(';
    $vals=') VALUES (';
    $first=true;
    foreach($data as $key=>$val)
    {
        $vals.=($first===true?'':', ')."'".mysqli_real_escape_string(db(),$val)."'";
        $fields.=($first===true?'':', ')."`".$key."`";
        $first=false;
    }
    $q.=$fields.$vals;
    $q.=')';
    return \ui\db\query($q);
}
function insert_batch($table,$datas)
{
    $q='INSERT INTO `'.\ui\config('sql_prefix').$table.'` ';
    $fields='(';
    $vals=') VALUES (';
    $first_row=$first=true;
    foreach($datas as $data)
    {
        if($first_row===false)
            $vals.=', (';
        $first=true;
        foreach($data as $key=>$val)
        {
            $vals.=($first===true?'':', ')."'".mysqli_real_escape_string(db(),$val)."'";
            if($first_row===true)
                $fields.=($first===true?'':', ')."`".$key."`";
            $first=false;
        }
        $vals.=')';
        $first_row=false;
    }
    $q.=$fields.$vals;
    return \ui\db\query($q);
}
function id()
{
  return mysqli_insert_id(db());
}
function update($table,$data,$suffix)//$suffix should be : WHERE id=123 LIMIT 2
{
    $q='UPDATE `'.\ui\config('sql_prefix').$table.'` SET ';
    $first=true;
    foreach($data as $key=>$val)
    {
        $q.=($first===true?'':', ')."`".$key."`='".mysqli_real_escape_string(db(),$val)."'";
        $first=false;
    }
    $q.=' '.$suffix;
    return \ui\db\query($q);
}
function delete($table,$suffix)
{
    $q='DELETE FROM `'.\ui\config('sql_prefix').$table.'` '.$suffix;
    return \ui\db\query($q);
}
function select($table,$fields=array('*'),$suffix='')
{
    $q='SELECT '.implode(',',$fields).' FROM `'.\ui\config('sql_prefix').$table.'` '.$suffix;
    return \ui\db\query($q);
}