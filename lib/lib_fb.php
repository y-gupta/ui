<?php
/**
 * This library handles facebook oauth, graph API and FQL
 * SVN Repo $URL: https://subversion.assembla.com/svn/theappbin/ui/trunk/lib/lib_fb.php $
 * @package ui\libraries\fb
 * @author Yash Gupta <technofreak777@gmail.com>
 * @version $Id: lib_fb.php 44 2013-09-22 16:06:36Z technofreak $
 * @copyright 2013 by @author - All Rights Reserved
 * @license http://theappbin.com/licence/ui Alternatively see LICENCE file included in the root directory of this script
 */
namespace ui\fb;
function &session($key=false,$val=null){
    static $s=array();
    if($key===false){
        return $s;
    }
    if(isset($s[$key])&&$val==null)
        return $s[$key];
    $s[$key]=$val;
    return $s[$key];
}
/**
 * Required for parsing signed_request
 */
function base64_url_encode($data) { 
  return rtrim(strtr(base64_encode($data), '+/', '-_'), '='); 
}
/**
 * Required for parsing signed_request
 */ 
function base64_url_decode($data) { 
  return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT)); 
}

function sr($secret=false,$sr=false) {
    if(!$sr&&isset($_REQUEST['signed_request']))
    {
      $sr=$_REQUEST['signed_request'];
    }
    if(!$sr)
      return false;
    if(!$secret)
      $secret=session('secret');
		list($encoded_sig, $payload) = explode('.', $sr, 2);
		$sig = base64_url_decode($encoded_sig);
		$data = json_decode(base64_url_decode($payload), true);

		if(strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
				error_log('Unknown algorithm. Expected HMAC-SHA256');
				return null;
		}

		$expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
		if($sig !== $expected_sig) {
				error_log('Bad Signed JSON signature!');
				return null;
		}
    session('sr',$data);
    if(isset($data['oauth_token'])){
      session('access_token',$data['oauth_token']);
      session('expires',$data['expires']);
    }
    return $data;
}

/**
 * Makes a POST request to facebook graph API
 * @param string $path The url path to make request to. Note that it *must start with a forward slash*, for e.g. /me/photo
 * @param array $params an associative array of parameters to pass. access_token is *not included by default*
 * @param bool $decode Wether to return json_decoded result, or raw results
 * @return array|string if decoded results are requested, an associative array is returned (not an object)
 */
function post($path,$params=array(),$decode=true,$set_token=true)
{
  if($set_token&&!isset($params['access_token'])){
    $params['access_token']=session('access_token');
  }
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,'https://graph.facebook.com'.$path);
	if(php_uname('n')=='INFIONMOVE'){
    curl_setopt($ch, CURLOPT_PROXY,'proxy62.iitd.ernet.in:3128');
  }
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  //to suppress the curl output 
	$result = curl_exec($ch);
	curl_close ($ch);
	return $decode?json_decode($result,1):$result;
}
/**
 * Makes a GET request to facebook graph API
 * @param string $path The url path to make request to. Note that it *must start with a forward slash*, for e.g. /me
 * @param array $params an associative array of parameters to pass. access_token is *not included by default*
 * @param bool $decode Wether to return json_decoded result, or raw results
 * @return array|string if decoded results are requested, an associative array is returned (not an object)
 */
function get($path,$params=array(),$decode=true,$set_token=true)
{
  if($set_token&&!isset($params['access_token'])){
    $params['access_token']=session('access_token');
  }
	$ch = curl_init();
	$url='https://graph.facebook.com'.$path.'?'.http_build_query($params);
	
    curl_setopt($ch, CURLOPT_URL,$url);
  if(php_uname('n')=='INFIONMOVE'){
    curl_setopt($ch, CURLOPT_PROXY,'proxy62.iitd.ernet.in:3128');
  }
  
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  //to suppress the curl output 
	$result = curl_exec($ch);
	curl_close($ch);
	return $decode?json_decode($result,1):$result;
}

/**
 * returns the facebook oauth dialog url to redirect to, in order o get the code and subsequently access_token/access_token
 * @param string $redirect_url This absolute url that facebook redirects to after the dialog. If set to false, the current url (without the query string) is used.
 * @return string The url to redirect to.
 */
function login_url($redirect_url=false,$permissions=array(),$session_key='state')
{
	$state=md5(uniqid(rand(), TRUE)); // CSRF protection;
	$_SESSION[$session_key]=$state;
    if($redirect_url=='')
        $redirect_url='http'.(!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!='off'?'s':'').'://'.$_SERVER['HTTP_HOST'].substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'].'?','?'));
	return "http://www.facebook.com/dialog/oauth?client_id=".\ui\config('fb_id')."&redirect_uri=" . urlencode($redirect_url)."&state=".$state.'&scope='.implode(',',$permissions);
}
/**
 * Get's the access_token from the code returned by facebook after an oauth dialog
 * If no $redirect_url is supplied, the url is tried to be extracted from
 * The current url on which the user is. Note that the query string is stripped from the url. 
 * Recommended if you are retrieving the token from the same url to which facebook redirects.
 * @param string $redirect_url This absolute url must be same as that you used while redirecting to the oauth dialog.
 * @return array array({token},{expiry time, absolute}) if successful, otherwise array(false,0)
 */
function token_from_code($code,$redirect_url=false)
//returns array of token and expiry timestamp
{
    if($redirect_url===false)
        $redirect_url='http'.(!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!='off'?'s':'').'://'.$_SERVER['HTTP_HOST'].substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'].'?','?'));
	$response = get('/oauth/access_token',array(
	 'client_id'=>\ui\config('fb_id'),
	 'redirect_uri'=>$redirect_url,
	 'client_secret'=>\ui\config('fb_secret'),
	 'code'=>$code),false);
	$params = null;
	parse_str($response, $params);
	if(isset($params['access_token'])){
		$token=$params['access_token'];
		$expires=6900+time();//default expiry is 2 hrs. subtracted 5 minutes for safe margin
        session('access_token',$token);
        session('expires',$expires);
		return array($token,$expires);
	}else error_log('\\ui\\fb\\token_from_code failed!'.PHP_EOL.$response.PHP_EOL);
	return array(false,0);
}
/**
 * Exchanges a short token for a long one
 * Note that if a short token has already been exchanged, it can't be exchanged again
 * and a long token (if passed) will also return an error.
 * @return array array({the long token},{expiry time, absolute}) on success, otherwise array(false,0)
 */
function exchange_token($token=false)
{
    if($token===false)
        $token=session('access_token');
	$response=get('/oauth/access_token',array('client_id'=>\ui\config('fb_id'),'client_secret'=>\ui\config('fb_secret'),
	'grant_type'=>'fb_exchange_token','fb_exchange_token'=>$token),false);
	$params=array();
	parse_str($response,$params);
	if(isset($params['access_token'])){
		$token=$params['access_token'];
		$expires=59*3600*24+time();//default expiry is 60 days. subtracted 1 day for safe margin
        session('access_token',$token);
        session('expires',$expires);
		return array($token,$expires);
	}else  error_log('\\ui\\fb\\exchange_token failed!'.PHP_EOL.$response.PHP_EOL);
	return array(false,0);
}
/**
 * Executes a FQL query
 * @param string|array $query A single FQL query, or an array of multiple queries
 * @return array json_decoded, associative array response from facebook
 */
function fql($query,$token=false)
{
    if($token===false)
        $token=session('access_token');
	if(is_array($query))$query=json_encode($query);
	return get('/fql',array('q'=>$query,'access_token'=>$token));
}
/**
 * Get an array of pages controlled by the current user
 * Each page is 
 * array(
 *   'id'=>{page id},
 *   'access_token'=>{page token},
 *   'name'=>{page name},
 *   'category'=>{page category},
 *   'perms'=>array(
 *       {some of 'ADMINISTER','EDIT_PROFILE','CREATE_CONTENT','MODERATE_CONTENT','CREATE_ADS','BASIC_ADMIN'}
 *     ),
 *   'expires'=>{absolute timestamp}
 * )
 * @return array An array of pages
 */
function get_pages($token=false,$expires=NULL)
//returned array is an array of array('id'=>'','access_token'=>'','name'=>'','category','perms','expires'=>absolute timestamp)
//'perms' is array of some/all of 'ADMINISTER','EDIT_PROFILE','CREATE_CONTENT','MODERATE_CONTENT','CREATE_ADS','BASIC_ADMIN'
{
    if($token===false)
        $token=session('access_token');
	$pages = get('/me/accounts',array('access_token'=>$token));
	$assets= array();
	if(isset($pages['data']))
	{
		if($expires)
			foreach($pages['data'] as &$page)
				$page['expires']=$expires;
		return $pages['data'];
	}
	else{
		error_log('\\ui\\fb\\get_pages FAILED. Response:'.PHP_EOL.print_r($pages,true).PHP_EOL);
		return array();
	}
}
/**
 * Sets status of a page/user/etc
 * @param string $target facebook id of page/user/group/etc
 * @param int $time the time of posting. A time less then 10 minutes later than current time will post the status immediately, and scheduling upto 6 months is allowed by facebook. Posts targeted beyond this return an error
 * @param string $link_name The anchor text of the link
 * @param string $link_caption This appears below the $link_name
 * @param string $link_desc This appears below the $link_name
 * @param string $link_img This is the publiccally accessible URL to the thumbnail. Provide images greater than 200x200 for better visibility.
 * @return array json_decoded response from facebook
 */
function status($target,$msg,$time=0,$token=false,$link='',$link_desc='',$link_img='',$link_name='',$link_caption='')
{
    if($token===false)
        $token=session('access_token');
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
	$result=post('/'.$target.'/feed/',$params);
	return $result;
}
/**
 * Post a photo to a page/user/etc
 * @param string $target facebook id of page/user/album/group/etc
 * @param string $photo The path to the image on the server (It is encoded into multipart form encoding by CURL)
 * @param int $time the time of posting. A time less then 10 minutes later than current time will post the status immediately, and scheduling upto 6 months is allowed by facebook. Posts targeted beyond this return an error
 * @return array json_decoded response from facebook
 */
function photo($target,$photo,$msg,$time=0,$token=false)
{
  if($token===false)
        $token=session('access_token');
	$params=array();
	if($time-time()>610&&$time-time()<3600*24*6*30)//maximum 6 months and minimum 10 minutes
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
	$result=post('/'.$target.'/photos/',$params);
	return $result;
}