<?php
namespace ui\auth2;
function user($field=false,$forced_user=false)
{
	static $user=false;
	$users=\ui\config('auth2_users');
	if($forced_user!==false)
	{
		$user=$forced_user;return true;
	}
	if($user===false){
		if($user=logged_in())
		{
			$user=$users[$user];unset($user['password']);
		}else{
			$user=$users['guest'];
		}
	}
	if($field===false)
		return $user;
	if(isset($user[$field]))
		return $user[$field];
	else
		return false;
}
function make_key($user,$pass,$time)
{
	return md5(\ui\config('salt').$pass.$user.$time.$_SERVER['HTTP_USER_AGENT']);
}
function can_access($perms,$block=true,$login_url='login')//returns true if given permissions are with the current user. if block=true, terminates the execution.
{
	if((user('level')&$perms)!==$perms)
	{
		//stop there!
		if($block)
		{
			if((user('level')&PERM_USER)===PERM_USER){?><div class="alert alert-danger"><b>Freeze!</b> This is a Restricted Area<br/>You don't have permissions to access this page. Go Somewhere else.</div><?php
			}elseif((user('level')&PERM_DEMO)===PERM_DEMO){?><div class="alert alert-danger"><b>Freeze!</b> This is a Restricted Area<br/>This page cannot be accessed in demo/read-only mode.</div><?php
			}else{?><div class="alert alert-danger">Did you forget to do something?<br/><a href='<?php echo $login_url ?>?redir=<?php echo urlencode($_SERVER['REQUEST_URI']) ?>'>Please Login</a> to Continue</div><?php
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
	if(isset($_SESSION[IID.'_login_key'])&&isset($_SESSION[IID.'_login_time'])&&isset($_SESSION[IID.'_login_user']))
	{
		$timestamp=$_SESSION[IID.'_login_time'];
		$user=$_SESSION[IID.'_login_user'];
		$users=\ui\config('auth2_users');
		if(isset($users[$user]))
		{
			$key=make_key($user,$users[$user]['password'],$timestamp);
			if($key===$_SESSION[IID.'_login_key'])
			{
				return $user;
			}
		}
	}elseif(isset($_COOKIE[IID.'_login_key'])&&isset($_COOKIE[IID.'_login_time'])&&isset($_COOKIE[IID.'_login_user']))
	{
		$timestamp=$_COOKIE[IID.'_login_time'];
		$user=$_COOKIE[IID.'_login_user'];
 	$users=\ui\config('auth2_users');
		if(isset($users[$user]))
		{
			$key=make_key($user,$users[$user]['password'],$timestamp);
			if($key===$_COOKIE[IID.'_login_key'])
			{
				$_SESSION[IID.'_login_time']=$timestamp;
				$_SESSION[IID.'_login_key']=$key;
				$_SESSION[IID.'_login_user']=$user;
				return $user;
			}		
		}
	}
	log_out();
	return false;
}
function log_in($name,$pass,$remember=true)
{
	$users=\ui\config('auth2_users');	
	if(!isset($users[$name]))//invalid user name
	{
	 log_out();
   return false;
  }
	$actual_pass=$users[$name]['password'];
	if($pass !== $actual_pass)
	{
		\ui\log('FAILED LOGIN ATTEMPT FROM '.$_SERVER['REMOTE_ADDR']);
    log_out();
		return false;	
	}
	if(!session_id())
		session_start();
	session_regenerate_id();
	$timestamp=time();
	$_SESSION[IID.'_login_time']=$timestamp;
	$_SESSION[IID.'_login_user']=$name;
	$_SESSION[IID.'_login_key']=make_key($name,$actual_pass,$timestamp);
	if($remember)
	{
		setcookie(IID.'_login_key',$_SESSION[IID.'_login_key'],$timestamp+3600*24*30,'/');
		setcookie(IID.'_login_user',$_SESSION[IID.'_login_user'],$timestamp+3600*24*30,'/');
		setcookie(IID.'_login_time',$timestamp,time()+3600*24*30,'/');
	}
	return true;
}
function log_out()
{
  $timestamp=time()-10;
	setcookie(IID.'_login_key',0,$timestamp,'/');
	setcookie(IID.'_login_time',0,$timestamp,'/');
	setcookie(IID.'_login_user',0,$timestamp,'/');
	if(!session_id())	
		session_start();
	if(isset($_SESSION[IID.'_login_key']))
		unset($_SESSION[IID.'_login_key']);
	if(isset($_SESSION[IID.'_login_time']))
		unset($_SESSION[IID.'_login_time']);
	if(isset($_SESSION[IID.'_login_user']))
		unset($_SESSION[IID.'_login_user']);
  $guest=\ui\config('users');$guest=$guest['guest'];
	user(false,$guest);
}