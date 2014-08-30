<?php
namespace ui\data;
/*
container - a file that stores the data
always force update the container when you are done with important data
*/
function &data()
{
	static $data=array();
	return $data;
}
function load($container,$reload=false)
{
	$data=&data();
	if(isset($data[$container])&&!$reload)
		return false;
	$fname=\ui\config('data_path').$container.\ui\config('data_file_suffix');
	if(!file_exists($fname)){
		$data[$container]=array();
		return false;
	}
	$content=file_get_contents($fname);
	$content=unserialize($content);
	if(!$content)
	{
		$data[$container]=array();
		return false;
	}
	$data[$container]=$content;
}
function update($specific=false)
{
	$all_data=&data();
	if(!$specific)
	{
		foreach($all_data as $container=>&$data)
		{
			file_put_contents(\ui\config('data_path').$container.\ui\config('data_file_suffix'),serialize($data));
		}
	}else{
		if(isset($all_data[$specific]))
		{
			file_put_contents(\ui\config('data_path').$specific.\ui\config('data_file_suffix'),serialize($all_data[$specific]));
			return true;
		}else return false;
	}
}
function &get($container,$key=false,$default=NULL,$create_if_not=true)
{
	$data=&data();
	if(!isset($data[$container]))
		load($container);
	if($key!==false&&$create_if_not&&!isset($data[$container][$key]))
		$data[$container][$key]=$default;
	if($key!==false&&isset($data[$container][$key]))
		return $data[$container][$key];
	if($key===false)
		return $data[$container];
	return $default;
}
function &set($container,$key,$val,$update=false)//if key===false, val is appended to the container array
{
	$data=&data();
	if(!isset($data[$container]))
	{
		load($container);
	}
	if($key!==false)
		$data[$container][$key]=$val;
	else
		$data[$container][]=$val;
	if($update)
		update($container);
	return $data[$container];
}
\ui\register_hook('exit','\ui\data\update');