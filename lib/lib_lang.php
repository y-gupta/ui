<?php
//Language Library for ui
//ver 1.1
namespace ui\lang{
    function load($locale=false)
    {	
        global $_LANG_POT;
    	if($locale==false){
            if(isset($_REQUEST['lang'])){
    			$locale=substr($_REQUEST['lang'],0,2);
    		}elseif(isset($_SESSION['lang_code'])){
    			$locale=$_SESSION['lang_code'];
    		}elseif(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'] ))
    		{
                $langs = array();
                // break up string into pieces (languages and q factors)
                preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $lang_parse);
                if(count($lang_parse[1])){
                    // create a list like "en" => 0.8
                    $langs = array_combine($lang_parse[1], $lang_parse[4]);
                    // set default to 1 for any without q factor
                    foreach ($langs as $lang => &$val) {
                        if ($val === '') $val = 1;
                        $val=(float)$val;
                    }
                   // sort list based on value	
                    arsort($langs, SORT_NUMERIC);
                }
                // Check them all, until we find a match
        		foreach ($langs as $locale=>$priority)
        		{
        			// Turn en-gb into en
        			$locale = strtolower(substr($locale, 0, 2));
        			// Check its in the array. If so, break the loop, we have one!
                    if(file_exists(\ui\global_var('app_dir').'lang/'.$locale.'.php'))
        				break;
        		}
    	   }
        }
    	$_LANG_POT=array();
       $locale=preg_replace('[^a-z]','',strtolower($locale));
    	if(!file_exists(\ui\global_var('app_dir').'lang/'.$locale.'.php'))
    		$locale=\ui\config('lang');
        else
            $_SESSION['lang_code']=$locale;
    	include('lang/'.$locale.'.php');
        $_LANG_POT['lang_code']=$locale;
        if(\ui\config('lang_write'))
        \ui\register_hook('exit','\\ui\\lang\\write');
    }
    function write(){
        //Automatically populate the language file.
        global $_LANG_POT;
        //file_get_contents('lang/'.$_LANG_POT['lang_code'].'.php',$php);
        $php='<'.'?php $_LANG_POT='.PHP_EOL.var_export($_LANG_POT,1).';';
        file_put_contents(\ui\global_var('app_dir').'lang/'.$_LANG_POT['lang_code'].'.php',$php);
    }
}
namespace{
$_LANG_POT=array();
	
    function __($key)//echoes the translated string
    {
        global $_LANG_POT;
    	if(!isset($_LANG_POT[$key]))
            $_LANG_POT[$key]=$key;
        vprintf($_LANG_POT[$key], array_slice(func_get_args(),1) ); 
    }
    function __js($key)//echoes the translated string for a js assign. json_encodes it.
    {
        global $_LANG_POT;
    	if(!isset($_LANG_POT[$key]))
            $_LANG_POT[$key]=$key;
    	echo json_encode(vsprintf($_LANG_POT[$key], array_slice(func_get_args(),1) )); 
    }
    function __html($key)//echoes the translated string for an HTML attribute htmlspecialchars() it
    {
        global $_LANG_POT;
    	if(!isset($_LANG_POT[$key]))
            $_LANG_POT[$key]=$key;
    	echo htmlspecialchars(vsprintf($_LANG_POT[$key], array_slice(func_get_args(),1) ),ENT_QUOTES); 
    }
    function __attr($key)//echoes the translated string for an HTML attribute htmlspecialchars(strip_tags()) it
    {
        global $_LANG_POT;
    	if(!isset($_LANG_POT[$key]))
            $_LANG_POT[$key]=$key;
    	echo htmlspecialchars(strip_tags(vsprintf($_LANG_POT[$key], array_slice(func_get_args(),1) )),ENT_QUOTES); 
    }
    function ___($key)//returns the translated string
    {
        global $_LANG_POT;
    	if(!isset($_LANG_POT[$key]))
            $_LANG_POT[$key]=$key;
    	return vsprintf($_LANG_POT[$key], array_slice(func_get_args(),1) ); 
    }
}