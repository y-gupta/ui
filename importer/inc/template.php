
<?php
$content=ob_get_clean();
if(defined('NO_TEMPLATE'))
{
	echo $content;
	return;
}
\ui\speedify\css('assets/css/bootstrap.css',false,'',1002);
\ui\speedify\css('assets/css/bootstrap-responsive.css',false,'',1001);
\ui\speedify\css('assets/css/social.css',false,'',1000);
\ui\speedify\js('assets/js/bootstrap.min.js');
ob_start();
?><!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <base href="<?php echo \ui\config('base_url') ?>" />
    <title><?php __(\ui\config('app_name')) ?> By TheAppBin.com</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Le styles -->
    <?php \ui\speedify\css() ?>
    <style>
	.align-radio{
		display: inline-block;
		padding-top:5px;
		vertical-align: middle;
	}
	.align-text{
		display: inline-block;padding-bottom:14px;padding-top:0;
		margin:0;vertical-align: middle;
	}
	  .tooltip-inner{	 
		box-shadow: 0px 0px 5px rgb(255, 255, 255);
		white-space: nowrap;
	  }
	  body {
		padding-top: 60px;
		padding-bottom: 40px;
	  }
	  @media (max-width: 979px) {
	  body{
	  	padding-top:0;
	  }
	  .navbar-fixed-bottom{
	  	position: fixed;
		margin:0;
	   }
	   .navbar-fixed-top .navbar-inner,.navbar-fixed-bottom .navbar-inner {
	    padding: 0px;
	   }
 	  }
	  .footer {
		  padding:2px;
		  font-size:12px;
		  border-top:#AAA solid thin;
		  background:#CCC;
	  } 
	  .center{
		  text-align:center;
	  }
	  .centerDiv{
		  margin:0 auto;
	  }
	  .hidden{
		  display:none;
	  }
	  .sub-section{
		  border-left:5px solid #666;
		  padding-left:5px;
		  margin-bottom:10px;
	  }
	 .accordion-heading{
		  background:#EEE;
	  }
      .vertical-right{
        position: fixed; 
        right:0px;
        top:50px;
    	-webkit-transform:rotate(270deg);
        -moz-transform:rotate(270deg);
        -o-transform: rotate(270deg);
        -webkit-transform-origin: bottom right;
        -moz-transform-origin: bottom right;
        -o-transform-origin: bottom right;
        filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=3);
        padding:3px;
        background: #F5F5F5;
        border:1px solid #DDD;
        border-bottom:0;
        -moz-border-radius-topleft: 5px;
        -moz-border-radius-topright:5px;
        -moz-border-radius-bottomleft:0px;
        -moz-border-radius-bottomright:0px;
        -webkit-border-top-left-radius:5px;
        -webkit-border-top-right-radius:5px;
        -webkit-border-bottom-left-radius:0px;
        -webkit-border-bottom-right-radius:0px;
        border-top-left-radius:5px;
        border-top-right-radius:5px;
        border-bottom-left-radius:0px;
        border-bottom-right-radius:0px;
      }   
    </style>
	<script>
	var DOMupdate_callbacks=[];
	function onDOMupdate(callback)
	{
		DOMupdate_callbacks.push(callback);
	}
	function DOMupdated(){
		for(i in DOMupdate_callbacks)
		{
			DOMupdate_callbacks[i]();
		}
	}
	</script>
    <script type="text/javascript" src="assets/js/jquery.js"></script>
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Fav and touch icons
    <link rel="shortcut icon" href="assets/img/logo.png">
    -->
  </head>

  <body>

    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="brand" href="#"><?php __(\ui\config('app_name')) ?></a>
            <ul class="nav">
              <?php
			  include('nav.php');		  
				foreach($_NAV as $item)
				{
					?><li class="<?php 
					$dropdown=isset($item[3]);
					if($dropdown){
						echo 'dropdown';
						if($_NAVBAR_ACTIVE==$item[0]||isset($item[3]{$_NAVBAR_ACTIVE}))echo ' active';
					}
					elseif($_NAVBAR_ACTIVE==$item[0])echo 'active';
					?>"><a href="<?php echo $item[0] ?>" title="<?php echo $item[1] ?>" class="tooltip-bottom tooltip-mobile<?php 
					if($dropdown)echo ' dropdown-toggle" data-toggle="dropdown';?>" style="padding:8px"><i class="icon-white icon-<?php echo $item[2] ?>"></i><span class="hidden-phone"> <?php echo $item[1];?></span><?php echo $dropdown?'<b class="caret"></b>':''; ?></a><?php
					if($dropdown)
					{
						?><ul class="dropdown-menu"><?php 
						foreach($item[3] as $subitem){
						?><li><a href="<?php echo $subitem[0] ?>"><i class="icon-<?php echo $subitem[2] ?>"></i> <?php echo $subitem[1] ?></a></li><?php
						}
						?></ul><?php
					}
					?></li><?php
				}?>
            </ul>
            <ul class="nav pull-right">
			<?php
			foreach($_NAV_RIGHT as $item)
			{
				?><li class="<?php 
				$dropdown=isset($item[3]);
				if($dropdown){
					echo 'dropdown';
					if($_NAVBAR_ACTIVE==$item[0]||isset($item[3]{$_NAVBAR_ACTIVE}))echo ' active';
				}
				elseif($_NAVBAR_ACTIVE==$item[0])echo 'active';
				?>"><a href="<?php echo $item[0] ?>" title="<?php echo $item[1] ?>" class="tooltip-bottom tooltip-mobile<?php 
				if($dropdown)echo ' dropdown-toggle" data-toggle="dropdown';?>" style="padding:8px"><i class="icon-<?php echo $item[2] ?> icon-white"></i><span class="hidden-phone"> <?php echo $item[1]; if($dropdown)echo '<b class="caret"></b>'; ?></span></a><?php
				if($dropdown)
				{
					?><ul class="dropdown-menu"><?php 
					foreach($item[3] as $subitem){
					?><li><a href="<?php echo $subitem[0] ?>" title="<?php echo $subitem[1] ?>" class="tooltip-left"><i class="icon-<?php echo $subitem[2] ?>"></i> <?php echo $subitem[1] ?></a></li><?php
					}
					?></ul><?php
				}
				?></li><?php
			}?>
            </ul>
        </div>
      </div>
    </div>
    <div class="container">
    <?php if($n_segments=count($_NAV_PATH)){?>
    <ul class="breadcrumb">
    <?php
	$i=0;
	foreach($_NAV_PATH as $url=>$name)
	{
		if($i==$n_segments-1)//last
		{
			?><li class="active"><?php __($name) ?></li><?php
		}else{
		?><li><a href="<?php echo \ui\get_url($url) ?>"><?php __($name) ?></a> <span class="divider">/</span></li><?php 
		}
		$i++;
	}
	?>
    </ul>
    <?php
	}//endif(nav_path)
	if($msg=\ui\get_flash('error')){
		?><div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $msg; ?></div><?php
	}
	if($msg=\ui\get_flash('success')){
		?><div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $msg; ?></div><?php
	}
	if($msg=\ui\get_flash('warning')){
		?><div class="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $msg; ?></div><?php
	}
	if($msg=\ui\get_flash('info')){
		?><div class="alert alert-info"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $msg; ?></div><?php
	}
     echo $content;
	?>
    <?php if(DEBUG){?>
<div class="accordion-group">
    <div class="accordion-heading">
      <a class="accordion-toggle" data-toggle="collapse" href="#debug_info">
      	DEBUG INFORMATION
      </a>
    </div>
    <div id="debug_info" class="accordion-body collapse">
      <div class="accordion-inner"><?php
		var_dump('config',\ui\global_var(),'session',$_SESSION,'request',$_REQUEST);
        ?></pre>
       </div>
    </div>
</div><?php } ?>
    </div> <!-- container -->
    <div class="navbar navbar-fixed-bottom footer">
    <div class="pull-left"></div>
    <div class="pull-right">Copyright &copy; <?php echo date('Y'); ?> &middot; All Rights Reserved &middot; <span class="badge badge-info"><?php __(\ui\config('app_name')) ?> by <a href='http://theappbin.com' style="color:#fff;"><big>TheAppBin.com</big></a></span></div></div>
	<script>
	function responsive_update(width)
	{
		if(width>979){//desktop
			$('.tooltip-mobile').tooltip('destroy');
		}else if(width>767){//tablet
			$('.tooltip-mobile').tooltip('destroy');		
		}else{//mobile
			$('.tooltip-mobile.tooltip-right').tooltip({'placement':'right','html':true});
			$('.tooltip-mobile.tooltip-top').tooltip({'placement':'top','html':true});
			$('.tooltip-mobile.tooltip-bottom').tooltip({'placement':'bottom','html':true});
			$('.tooltip-mobile.tooltip-left').tooltip({'placement':'left','html':true});
		}
	}
	onDOMupdate(function(){
		
		$('.tooltip-bottom').tooltip({'placement':'bottom','html':true});
		$('.tooltip-top').tooltip({'placement':'top','html':true});
		$('.tooltip-left').tooltip({'placement':'left','html':true});
		$('.tooltip-right').tooltip({'placement':'right','html':true});
		
		$('.uneditable').each(function(){ $(this).attr('original',$(this).val()); });
		$('.autoselect').mousedown(function(event){$(this).select();return false;});		
		$('.uneditable').change(function(){$(this).val($(this).attr('original'));});
		//$('.uneditable').keydown(function(){return false});  //enabling this blocks all keyboard keys till focus is on
		responsive_update($(window).width());
	});
	$(function(){
		DOMupdated();
		$(window).resize(function(){
			responsive_update($(this).width());
			});
		$(document).mousedown(fixMB_down);
		$(document).mouseup(fixMB_up);
	});
	function fixMB_down(e){
		// Left mouse button was pressed, set flag
		if(e.which === 1) leftButtonDown = true;
	}
	function fixMB_up(e){
		// Left mouse button was released, clear flag
		if(e.which === 1) leftButtonDown = false;
	}
	var leftButtonDown = false;
	function fixMBstatus(e){
		// Check from jQuery UI for IE versions < 9
		if ($.browser.msie && !(document.documentMode >= 9) && !event.button)
			leftButtonDown = false;
		// If left button is not set, set which to 0
		// This indicates no buttons pressed
		if(e.which === 1 && !leftButtonDown)
			e.which = 0;
	}
    </script>
    <?php \ui\speedify\js(); \ui\speedify\js_code(); ?>
  </body>
</html><?php
\ui\benchmark('Output sent to Browser');
\ui\benchmark(NULL,true);