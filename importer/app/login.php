<?php
if(isset($_GET['logout']))
{
	\ui\auth\log_out();
	?>
	<div class="alert alert-success"><?php __('You have been logged out.') ?></div>
	<?php
	exit;
}
// \ui\auth\load_users(\ui\data\get('user'));
if(\ui\auth\logged_in())
{
	?><div class="alert alert-warning"><b><?php __('Whoa') ?>!</b> <?php __('You\'re already logged in') ?><br/><?php __('If you are not <b>%s</b>, please <a href="%s">click here to logout</a>',\ui\auth\user('name'),\ui\get_url('login?logout')) ?></div>
	<?php	
	exit;
}
if(isset($_POST['submit']))
{
	if(\ui\auth\log_in($_POST['name'],$_POST['pass'],isset($_POST['remember'])?true:false))
	{
		header('Location: '.(isset($_GET['redir'])?$_GET['redir']:\ui\get_url('')));
		exit;
	}else{
		?>
        <div class="alert alert-error"><b>Oops!</b> It seems that you entered a wrong username/password combination.<br/>Please try again, or edit <i>/config.php</i> file to reset the admin password.</div>
		<?php
	}
}
?><div class="centerDiv" style="width:230px;">
<form method="post">
    <label class="control-label" for="inputUname"><?php __('User Name') ?></label>
      <input type="text" id="inputUname" name="name" placeholder="User Name">
    <label class="control-label" for="inputPassword"><?php __('Password') ?></label>
      <input type="password" id="inputPassword" name="pass" placeholder="Password">
      <label class="checkbox">
        <input type="checkbox" name="remember" checked="checked"> <?php __('Keep me Logged in') ?>
      </label>
  <input type="submit" class="btn btn-primary" name="submit" value="Login" />
  <button class="btn" type="reset">Clear</button>
</div>
</form>