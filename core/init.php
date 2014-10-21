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
//custom initialization goes here