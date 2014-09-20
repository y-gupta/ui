<?php
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
