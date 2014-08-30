<?php
$_NAV=array();	
$_NAV_RIGHT=array();
$_nav_path=explode('/',\ui\global_var('path'));
$_NAV_PATH=array(''=>'Home');
$tmp='';
$_NAVBAR_ACTIVE=\ui\global_var('controller');
foreach($_nav_path as &$segment)
{
	if($segment=='')continue;
	$tmp.=$segment.'/';
	$_NAV_PATH[$tmp]=ucwords($segment);
}
if(\ui\auth\can_access(PERM_USER,false))
{	  
	$_NAV[]=array('dashboard','Dashboard','home');//url,name,icon
	$_NAV[]=array('#','Scheduler','time',array(
		'scheduler'=>array('scheduler','Manage','check'),
		'scheduler/new'=>array('scheduler/new','Create New','plus-sign')
	));
	$_NAV[]=array('#','Jobs','briefcase',
	array(
		'job'=>array('job','Manage','check'),
		'job/new'=>array('job/new','Create New','plus-sign')
	));
	$_NAV[]=array('#','Libraries','picture',array(
		'library'=>array('library','Manage','check'),
		'library/new'=>array('library/new','Create New','plus-sign')
	));
	$_NAV[]=array('#','Targets','bullhorn',array(
		'target'=>array('target','Manage All','check'),
		'facebook'=>array('facebook','Facebook','facebook icon-social'),
		'tumblr'=>array('tumblr','Tumblr','tumblr icon-social'),
		'wordpress'=>array('wordpress','Wordpress','wordpress icon-social')
	));
	if(\ui\auth\can_access(PERM_ADMIN,false))
	{
		$_NAV[]=array('users','Users','user');
	}
	$_NAV[]=array('#','Help','question-sign',array(
			'contact'=>array('contact','Contact Support','envelope'),
			'readme'=>array('readme','Documentation','file')
		));
	$_NAV_RIGHT[]=array('login?logout','Logout','off');
}else{
	$_NAV[]=array('contact','Contact Support','envelope');
	$_NAV_RIGHT[]=array('login','Login','off');
}