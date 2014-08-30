<?php
$config['app_name']='PostBot';
$config['salt']='12387tdq874365fqd89rotyao3hto';
$config['users']=array(
    'admin'=>array('paringari',PERM_ADMIN),
    'demo'=>array('123pass',PERM_DEMO)
);
if(!FROM_CLI){//yay...we are on a web browser 
    $config['base_url_relative']=rtrim(dirname($_SERVER['SCRIPT_NAME']),'/').'/';//relative URL to base directory
    $config['base_url']='http'.(!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!='off'?'s':'').'://'.$_SERVER['HTTP_HOST'].$config['base_url_relative'];//absolute URL to base directory
}
$config['base_path']=realpath(dirname(__FILE__).'/..').'/';

$config['cache_path']=$config['base_path'].'cache/';
$config['cache_file_suffix']='.txt';
$config['cache_timeout']=600;

$config['data_path']=$config['base_path'].'data/';
$config['data_file_suffix']='.txt';

$config['content_library']='content-library';

$config['pretty_url']=true;//whether using pretty URL's

$config['lang_write']=true;
$config['lang']='en';

$config['sql_host']='localhost';
$config['sql_user']='root';
$config['sql_pass']='password';
$config['sql_db']='yash_data';
$config['sql_prefix']='pb_';
$config['sql_port']=null;
$config['sql_socket']=null;

if(!isset($_SERVER['COMPUTERNAME'])||$_SERVER['COMPUTERNAME']!='INFIONMOVE')
{
$config['sql_user']='cronpost_yash';
$config['sql_pass']='paringari';
$config['sql_db']='cronpost_yash';
}

$config['fb_id']='461170280590363';
$config['fb_secret']='d28e8f65f7e9d9dce65611e93e292595';

$config['bitly_login']='thetechnofreak';
$config['bitly_api_key']='R_c3e689c751cfa6f2157f4ba6814353da';