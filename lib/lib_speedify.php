<?php
namespace ui\speedify;
function &data(){
	static $data=array('js'=>array(),'css'=>array(),'js_code_00'=>array(),'js_code_01'=>array(),'js_code_10'=>array(),'js_code_11'=>array());
	return $data;
}
function css($src=false,$iscode=false,$attr='',$priority=100)
//higher the priority, earlier the css is echoed
//if $iscode is true, the value of $src is echoed as css in a <style> [the file (if any) is NOT read]
//leave src empty to echo the css HTML.
//attr is the string to insert in the <link [...] > tag
//echoed just before </head>
{
	$data=&data();
	if($src===false){
		$sort_column = array();
		foreach ($data['css'] as $key=>$css)
			$sort_column[$key]=$css[3];
		arsort($sort_column);
		foreach($sort_column as $key=>$priority)
		{
			if(!$data['css'][$key][1]){?><link rel="stylesheet" type="text/css" href="<?php echo $data['css'][$key][0] ?>" <?php echo $data['css'][$key][2] ?> /><?php
			}else{
				?><style type="text/css" <?php echo $data['css'][$key][2] ?>><?php echo $data['css'][$key][0] ?></style><?php
			}
		}
		return;
	}
	$data['css'][]=array($src,$iscode,$attr,$priority);
	return true;
}
function js($src=false,$attr='')
//leave src empty to echo the script HTML.
//attr is the string to insert in the <link [...] > tag
//echoed just before </body>
{
	$data=&data();
	if($src===false){
		foreach($data['js'] as $js)
		{
			?><script type="text/javascript" src="<?php echo $js[0] ?>" <?php echo $js[1] ?>></script><?php
		}
		return;
	}
	$data['js'][]=array($src,$attr);
	return true;
}
function js_code($code=false,$global=true,$wrap=true)
//if global is set, code is merged with other global codes of same wrap injected.
//no need to wrap in $(function(){}); (if wrap=true)
//echoed just before </body>
{
	$data=&data();
	if($code===false){
		?><script type="text/javascript">
		$(function(){<?php foreach($data['js_code_11'] as $js)echo $js;?>});
		<?php foreach($data['js_code_10'] as $js)echo $js;?>
		<?php foreach($data['js_code_00'] as $js)echo '{',$js,'}';?>
		<?php foreach($data['js_code_01'] as $js)echo '$(function(){',$js,'});';?>		
        </script><?php
		return;
	}
	if($global&&$wrap)
		$data['js_code_11'][]=$code;
	elseif($global)
		$data['js_code_10'][]=$code;
	elseif($nowrap)
		$data['js_code_01'][]=$code;
	else
		$data['js_code_00'][]=$code;
	return true;
}