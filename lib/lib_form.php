<?php
namespace  ui\form;
/*
$options can have:
id
slug
attr
class
inline
*/
require_once('recaptchalib.php');
function captcha(){
  echo recaptcha_get_html(\ui\config('recaptcha_public_key'));
}
function check_captcha(){
	if($_SERVER["REMOTE_ADDR"]=='::1')
		return true;
  $resp=recaptcha_check_answer (\ui\config('recaptcha_private_key'),
                                $_SERVER["REMOTE_ADDR"],
                                isset($_POST["recaptcha_challenge_field"])?$_POST["recaptcha_challenge_field"]:'',
                                isset($_POST["recaptcha_response_field"])?$_POST["recaptcha_response_field"]:'');
  return $resp->is_valid;
}
function text($key,$label=false,$value='',$options=array())
{
	$value=htmlspecialchars($value, ENT_QUOTES);
	static $counter=0;
	if($label===false)$label=$key;
	if(!isset($options['slug']))$options['slug']=++$counter;
	if(!isset($options['id']))
		$options['id']=$options['slug'].'_'.$key;
	echo '<label for="'.$options['id'].'">'.$label.'</label>'.PHP_EOL;
	echo '<input type="text" class="'.(isset($options['class'])?$options['class']:'').'" name="'.$key.'" id="'.$options['id'].'" value="'.$value.'" '.(isset($options['attr'])?$options['attr']:'').' />';
}

function hidden($key,$value='',$options=array())
{
	$value=htmlspecialchars($value, ENT_QUOTES);
	static $counter=0;
	if(!isset($options['slug']))$options['slug']=++$counter;
	if(!isset($options['id']))
		$options['id']=$options['slug'].'_'.$key;
	echo '<input type="hidden" class="'.(isset($options['class'])?$options['class']:'').'" name="'.$key.'" id="'.$options['id'].'" value="'.$value.'" '.(isset($options['attr'])?$options['attr']:'').' />';
}
function password($key,$label=false,$value='',$options=array())
{
	$value=htmlspecialchars($value, ENT_QUOTES);
	static $counter=0;
	if($label===false)$label=$key;
	if(!isset($options['slug']))$options['slug']=++$counter;
	if(!isset($options['id']))
		$options['id']=$options['slug'].'_'.$key;
	echo '<label for="'.$options['id'].'">'.$label.'</label>'.PHP_EOL;
	echo '<input type="password" class="'.(isset($options['class'])?$options['class']:'').'" name="'.$key.'" id="'.$options['id'].'" value="'.$value.'" '.(isset($options['attr'])?$options['attr']:'').' />';
}
function checkbox($key,$label=false,$checked=false,$value='',$options=array())
{
	$value=htmlspecialchars($value, ENT_QUOTES);
	static $counter=0;
	if($label===false)$label=$key;
	if(!isset($options['slug']))$options['slug']=++$counter;
	if(!isset($options['id']))
		$options['id']=$options['slug'].'_'.$key;
	echo '<label for="'.$options['id'].'" class="checkbox'.(isset($options['inline'])&&$options['inline']?' inline':'').'">';
	echo '<input class="'.(isset($options['class'])?$options['class']:'').'" type="checkbox" name="'.$key.'" id="'.$options['id'].'" value="'.$value.'" '.($checked?'checked="checked" ':'').(isset($options['attr'])?$options['attr']:'').' />'.
	$label.'</label>';
}
function radio($key,$label=false,$checked=false,$value='',$options=array())
{
	$value=htmlspecialchars($value, ENT_QUOTES);
	static $counter=0;
	if($label===false)$label=$key;
	if(!isset($options['slug']))$options['slug']='radio'.++$counter;
	if(!isset($options['id']))
		$options['id']=$options['slug'].'_'.$key;
	echo '<label for="'.$options['id'].'" class="radio'.(isset($options['inline'])&&$options['inline']?' inline':'').'">';
	echo '<input class="'.(isset($options['class'])?$options['class']:'').'" type="radio" name="'.$key.'" id="'.$options['id'].'" value="'.$value.'" '.($checked?'checked="checked" ':'').(isset($options['attr'])?$options['attr']:'').' />'.$label.'</label>';
}
function textarea($key,$label=false,$value='',$rows=3,$options=array())
{
	$value=htmlspecialchars($value, ENT_QUOTES);
	static $counter=0;
	if($label===false)$label=$key;
	if(!isset($options['slug']))$options['slug']=++$counter;
	if(!isset($options['id']))
		$options['id']=$options['slug'].'_'.$key;
	echo '<label for="'.$options['id'].'">'.$label.'</label>';
	echo '<textarea class="'.(isset($options['class'])?$options['class']:'').'" name="'.$key.'" rows="'.$rows.'" id="'.$options['id'].'" '.(isset($options['attr'])?$options['attr']:'').'>'.$value.'</textarea>';
}

//TODO:
function select($key,$label,$values,$selected=false,$options){
  $value=htmlspecialchars($value, ENT_QUOTES);
	static $counter=0;
	if($label===false)$label=$key;
	if(!isset($options['slug']))$options['slug']=++$counter;
	if(!isset($options['id']))
		$options['id']=$options['slug'].'_'.$key;
	echo '<label for="'.$options['id'].'" class="radio'.(isset($options['inline'])&&$options['inline']?' inline':'').'">';
	echo '<input class="'.(isset($options['class'])?$options['class']:'').'" type="radio" name="'.$key.'" id="'.$options['id'].'" value="'.$value.'" '.($checked?'checked="checked" ':'').(isset($options['attr'])?$options['attr']:'').' />'.$label.'</label>';
}
