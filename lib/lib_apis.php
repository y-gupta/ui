<?php
namespace ui\apis;
/*
Library for interacting with various External Services through their Web API's
Configuration in config.php:
'bitly'=>array(
	login=>[your login username]
	api_key=>[your API key]
)
'facebook'=>array(
	id=>[your app id]
	secret=>[your app secret]
	namespace=>[your app namespace] (blank/not set for no namespace)
)
'tumblr'=>array(
)
*/
function config($api,$key=false)
{
	$config=\ui\config($api);
	if($key){if(isset($config[$key]))
		return $config[$key];
	else return NULL;
	}
	return $config;
}
function bitly_short($url) {	
	$config=config('bitly');
	$api_call = file_get_contents("http://api.bit.ly/shorten?version=2.0.1&longUrl=".urlencode($url)."&login=".$config['login']."&apiKey=".$config['api_key']);
	$bitlyinfo=json_decode(utf8_encode($api_call),true);
	if ($bitlyinfo['errorCode']==0&&$bitlyinfo['results'][($url)]['shortUrl']) {
		return $bitlyinfo['results'][($url)]['shortUrl'];
	}
	return $url;//return the long url itself
}
function base64_url_encode($data) { 
  return rtrim(strtr(base64_encode($data), '+/', '-_'), '='); 
} 
function base64_url_decode($data) { 
  return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT)); 
}
function fb_post($path,$params=array(),$decode=true)
//path as str in {} in https://graph.facebook.com{/me}, $params array of params.
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,'https://graph.facebook.com'.$path);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  //to suppress the curl output 
	$result = curl_exec($ch);
	curl_close ($ch);
	return $decode?json_decode($result,1):$result;
}
function fb_get($path,$params=array(),$decode=true)
{
	$ch = curl_init();
	$url='https://graph.facebook.com'.$path.'?'.http_build_query($params);
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  //to suppress the curl output 
	$result = curl_exec($ch);
	curl_close ($ch);
	return $decode?json_decode($result,1):$result;
}
function fb_login_url($redirect_to,$permissions=array(),$session_key='state')
{
	$state=md5(uniqid(rand(), TRUE)); // CSRF protection;
	$_SESSION[$session_key]=$state;
	return "http://www.facebook.com/dialog/oauth?client_id=".config('facebook','id')."&redirect_uri=" . urlencode(\ui\get_url('facebook/renew'))."&state=".$state.'&scope='.implode(',',$permissions);
}
function fb_token_from_code($code,$redirect_url)
//returns array od token and expiry timestamp
{
	$config=config('facebook');
	$response = fb_get('/oauth/access_token',array(
	 'client_id'=>$config['id'],
	 'redirect_uri'=>$redirect_url,
	 'client_secret'=>$config['secret'],
	 'code'=>$code),false);
	$params = null;
	parse_str($response, $params);
	if(isset($params['access_token'])){
		$token=$params['access_token'];
		$expires=6900+time();//default expiry is 2 hrs. subtracted 5 minutes for safe margin
		return array($token,$expires);
	}else error_log('\\ui\\apis\\fb_token_from_code failed!'.PHP_EOL.$response.PHP_EOL);
	return array(0,0);
}
function fb_exchange_token($token)
//exchanges short lived token for long life token
{
	$config=config('facebook');
	$response=fb_get('/oauth/access_token',array('client_id'=>$config['id'],'client_secret'=>$config['secret'],
	'grant_type'=>'fb_exchange_token','fb_exchange_token'=>$token),false);
	$params=array();
	parse_str($response,$params);
	if(isset($params['access_token'])){
		$token=$params['access_token'];
		$expires=59*3600*24+time();//default expiry is 60 days. subtracted 1 day for safe margin
		return array($token,$expires);
	}else  error_log('\\ui\\apis\\fb_exchange_token failed!'.PHP_EOL.$response.PHP_EOL);
	return array(0,0);
}
function fb_fql($query,$token)
//a single query or an associative array of queries
{
	if(is_array($query))$query=json_encode($query);
	return fb_get('/fql',array('q'=>$query,'access_token'=>$token));
}
function fb_get_pages($token,$expires=NULL)
//returned array is an array of array('id'=>'','access_token'=>'','name'=>'','category','perms','expires'=>absolute timestamp)
//'perms' is array of some/all of 'ADMINISTER','EDIT_PROFILE','CREATE_CONTENT','MODERATE_CONTENT','CREATE_ADS','BASIC_ADMIN'
{
	$pages = fb_get('/me/accounts',array('access_token'=>$token));
	$assets= array();
	if(isset($pages['data']))
	{
		if($expires)
			foreach($pages['data'] as &$page)
				$page['expires']=$expires;
		return $pages['data'];
	}
	else{
		error_log('\\ui\\apis\\fb_get_pages FAILED. Response:'.PHP_EOL.print_r($pages,true).PHP_EOL);
		return array();
	}
}

function fb_status($target,$msg,$token,$time=0,$link='',$link_desc='',$link_img='',$link_name='',$link_caption='')
//posts a status update
{
	$params=array();
//		$params['actions']=json_encode(array('name'=>'grab it','link'=>'http://google.com/'));
	if($time-time()>610&&$time-time()<3600*24*6)//maximum 6 months and minimum 10 minutes
	{
		$params['scheduled_publish_time']=$time;
		$params['published']=false;
	}elseif($time-time()<610)//error of 10 minutes is O.K.
	{
		$params['published']=true;	
	}else{
		return array('error'=>array('message'=>'Invalid Schedule Time'));
	}
	$params['access_token']=$token;
	$params['message']=$msg;
	if($link)
	{
		$params['link']=$link;
		if($link_img)$params['picture']=$link_img;
		if($link_name)$params['name']=$link_name;
		if($link_caption)$params['caption']=$link_caption;
		if($link_desc)$params['description']=$link_desc;
	}
	$params['access_token']=$token;
	$result=\ui\apis\fb_post('/'.$target.'/feed/',$params);
	return $result;
}
function fb_photo($target,$photo,$msg,$token,$time=0)
//Posts a photo to facebook. target can be an album id
{
	$params=array();
	if($time-time()>610&&$time-time()<3600*24*6)//maximum 6 months and minimum 10 minutes
	{
		$params['scheduled_publish_time']=$time;
		$params['published']=false;
	}elseif($time-time()<610)//error of 10 minutes is O.K.
	{
		$params['published']=true;	
	}else{
		return array('error'=>array('message'=>'Invalid Schedule Time'));
	}
	$params['message']=$msg;
	$params['source']='@'.realpath($photo);
	$params['access_token']=$token;
	if($link)
	{
		$params['link']=$link;
		if($link_img)$params['picture']=$link_img;
		if($link_name)$params['name']=$link_name;
		if($link_caption)$params['caption']=$link_caption;
		if($link_desc)$params['description']=$link_desc;
	}
	$result=\ui\apis\fb_post('/'.$target.'/photos/',$params);
	return $result;
}