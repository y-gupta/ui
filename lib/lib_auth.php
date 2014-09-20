<?php
/**
 * user login and authentication management for users from a database
 * Specify the table name in config.php as 'auth_table'=>'table name'
 * Table should have atleast:
 * id
 * email
 * password
 * level
 */
namespace ui\auth;
\ui\load_lib('db');
function &user($field=false,$set_to_this=false)
{
	static $user=false;
	if($set_to_this!==false)
	{
		$user=$set_to_this;
    return $user;
	}
	if($user===false)
	 	$user=logged_in();
	if($field===false)
		return $user;
	if(isset($user[$field]))
		return $user[$field];
	$user[$field]=false;
	return $user[$field];
}
function make_key($email,$pass,$time)
{
	return hash('sha256',\ui\config('salt').$pass.$email.$time.$_SERVER['HTTP_USER_AGENT']);
}

function pass($in,$salt1='')
{
  if($salt1==''){
  for($i=0;$i<8;$i++)
    $salt1.=chr(mt_rand(40,126));
  }
  $h=$salt1.hash('sha256',$salt1.\ui\config('salt').$in);
  return $h;
}
function check($in,$hash)
{
  $h=pass($in,substr($hash,0,8));
  return $h===$hash;
}
/**
 * returns true if given permissions are with the current user. if block=true, terminats the execution.
 */
function can_access($perms,$block=true)
{
  $l=user('level');
  if(($l&$perms)!==$perms)
	{
		//stop there!
		if($block)
		{
			if(($l&PERM_USER)===PERM_USER){?><div class="alert alert-danger"><b>Oh no!</b><br/>You don't have permissions to access this page.</div><?php
			}elseif(($l&PERM_DEMO)===PERM_DEMO){?><div class="alert alert-danger"><b>This is a Restricted Area</b><br/>This page cannot be accessed in demo/read-only mode.</div><?php
			}else{?><div class="alert alert-error"><a href='login?redir=<?php echo urlencode($_SERVER['REQUEST_URI']) ?>'>Please Login</a> to Continue</div><?php
			}
			exit();
		}
		return false;
	}
	return true;//every thing fine.
}
function logged_in()
{
  if(!session_id())
	session_start();
  $guest=\ui\config('auth_guest');
  $timestamp=0;
  if(isset($_SESSION[IID.'_login_key'])&&isset($_SESSION[IID.'_login_time'])&&isset($_SESSION[IID.'_login_email']))
  {
    $timestamp=$_SESSION[IID.'_login_time'];
    $email=$_SESSION[IID.'_login_email'];
    $key=$_SESSION[IID.'_login_key'];
  }elseif(isset($_COOKIE[IID.'_login_key'])&&isset($_COOKIE[IID.'_login_time'])&&isset($_COOKIE[IID.'_login_email']))
  {
 	  $timestamp=$_COOKIE[IID.'_login_time'];
    $email=$_COOKIE[IID.'_login_email'];
    $key=$_COOKIE[IID.'_login_key'];
  }else{
    log_out();
    return $guest;
  }
  \ui\db\select(\ui\config('auth_table'),array('*'),"WHERE email='".\ui\db\escape($email)."' LIMIT 1");
  $user=\ui\db\assoc();
  if(!$user){
    //The user doesent exist. See if it is the hardcoded admin
    $admin=\ui\config('auth_admin');
    if($email===$admin['email'])
    {
      $user=$admin;
      $user['password']=pass($admin['password'],substr(\ui\config('salt'),0,8));
    }
  }
  if($user)
	{
	 $key1=make_key($user['email'],$user['password'],$timestamp);
		if($key===$key1){
			return $user;
		}
	}
	log_out();
	return $guest;
}
function log_in($email,$pass,$remember=true)
{
  $user=&user();
  \ui\db\select(\ui\config('auth_table'),array('*'),"WHERE email='".\ui\db\escape($email)."' LIMIT 1");
  $user=\ui\db\assoc();
  if(!$user){
    $admin=\ui\config('auth_admin');
    if($email===$admin['email']){
      $user=$admin;
      $user['password']=pass($admin['password'],substr(\ui\config('salt'),0,8));
    }
  }
  if(!$user)
    return false;
	if(!check($pass,$user['password']))
	{
    $user=array();
		if(DEBUG)
      error_log('FAILED LOGIN ATTEMPT FROM '.$_SERVER['REMOTE_ADDR'].' ON '.date('M d,Y h:i:s a P').PHP_EOL);
		return false;
	}
	if(!session_id())
		session_start();
	session_regenerate_id();
	$timestamp=time();
	$_SESSION[IID.'_login_time']=$timestamp;
	$_SESSION[IID.'_login_email']=$user['email'];
	$_SESSION[IID.'_login_key']=make_key($user['email'],$user['password'],$timestamp);
	if($remember)
	{
		setcookie(IID.'_login_key',$_SESSION[IID.'_login_key'],$timestamp+3600*24*30,'/');
		setcookie(IID.'_login_email',$_SESSION[IID.'_login_email'],$timestamp+3600*24*30,'/');
		setcookie(IID.'_login_time',$timestamp,$timestamp+3600*24*30,'/');
	}
	return true;
}
function log_out()
{
  $timestamp=time();
	setcookie(IID.'_login_key',0,$timestamp-1000,'/');
	setcookie(IID.'_login_time',0,$timestamp-1000,'/');
	setcookie(IID.'_login_email',0,$timestamp-1000,'/');
	if(!session_id())
		session_start();
	if(isset($_SESSION[IID.'_login_key']))
		unset($_SESSION[IID.'_login_key']);
	if(isset($_SESSION[IID.'_login_time']))
		unset($_SESSION[IID.'_login_time']);
	if(isset($_SESSION[IID.'_login_email']))
		unset($_SESSION[IID.'_login_email']);
	user(false,array());
}
