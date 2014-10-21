<?php
register_shutdown_function('\\ui\\on_exit');
ob_start();
session_start();

if(!isset($_APP_DIR)){
  $_APP_DIR=dirname(__FILE__).'/';
  \ui\global_var('imported_app',false,1);
}else
  \ui\global_var('imported_app',true,1);
\ui\global_var('app_dir',$_APP_DIR,true);
define('IID',substr(md5($_APP_DIR),0,8));//Instance ID, to prevent session variable collisions
\ui\benchmark('Loaded Base UI');
if(DEBUG)
  error_reporting(E_ALL);
else
  error_reporting(0);
\ui\benchmark();

include($_APP_DIR.'inc/autoload.php');
foreach($autoload['lib'] as $ui_lib)
{
	\ui\load_lib($ui_lib);
}
unset($ui_lib);
foreach($autoload['plugin'] as $ui_plugin)
{
	\ui\load_plugin($ui_plugin);
}
unset($ui_plugin);
unset($autoload);
{
  $ctrl='/'.\ui\global_var('controller');
  $offset=strpos($ctrl,'/');
  $inc_dir=substr($ctrl,0,$offset);
  $inc_paths=array();
  while($offset!==false){
    $inc_path=$_APP_DIR.'app'.$inc_dir.'/_include.php';
    if(file_exists($inc_path)){
      $inc_paths[]=$inc_path;
      include($inc_path);
    }
    $offset=strpos($ctrl,'/',$offset+1);
    if($offset===false)
      break;
    $inc_dir=substr($ctrl,0,$offset);
  }
  \ui\global_var('includes',$inc_paths,1);
  unset($ctrl);
  unset($inc_dir);
  unset($offset);
}
