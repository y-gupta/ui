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
//custom initialization goes here