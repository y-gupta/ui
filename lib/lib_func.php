<?php
namespace ui\func;
class auto{
  const load=0;
  public static function testSuite(){
  }
}
class Enum {
    protected $self = array();
    public function __construct( /*...*/ ) {
        $args = func_get_args();
        for( $i=0, $n=count($args); $i<$n; $i++ )
            $this->add($args[$i]);
    }
    
    public function __get( /*string*/ $name = null ) {
        return $this->self[$name];
    }
    
    public function add( /*string*/ $name = null, /*int*/ $enum = null ) {
        if( isset($enum) )
            $this->self[$name] = $enum;
        else
            $this->self[$name] = end($this->self) + 1;
    }
}
class FlagsEnum extends Enum {
    public function __construct( /*...*/ ) {
        $args = func_get_args();
        for( $i=0, $n=count($args), $f=0x1; $i<$n; $i++, $f *= 0x2 )
            $this->add($args[$i], $f);
    }
}

function similar_phase($a,$b){
  $a=metaphone($a);
  $b=metaphone($b);
  $na=strlen($a);$nb=strlen($b);
  $n=$nb;
  if($na>$nb)
    $n=$na;
  return (levenshtein($a,$b)*100.0)/$n;
}
function thumbnail($src,$thumb){
    //thumbnailer
    $post_src=@imagecreatefromstring(file_get_contents($src));
    if(!$post_src){
        $type=pathinfo($src,PATHINFO_EXTENSION);
        if($type=='jpeg'||$type=='jpg')
            $post_src=imagecreatefromjpeg($src);
        elseif($type=='png')
            $post_src=imagecreatefrompng($src);
        elseif($type=='gif')
            $post_src=imagecreatefromgif($src);
        else{
          error_log("\ui\func\thumbnail(): Image format not recognised. src:$src");
          return false;
        }
        if(!$post_src){
           error_log("\ui\func\thumbnail(): Image format not recognised. src:$src");
          return false;
        }
    }
    $srcx=imagesx($post_src);
    $srcy=imagesy($post_src);
    $tarx=\ui\config('thumb_width');
    $tary=\ui\config('thumb_height');
    if($tarx==0)$tarx=$srcx/$srcy*$tary;
    if($tary==0)$tary=$srcy/$srcx*$tarx;
    $factor=$srcx/$tarx;
    $dfactor=1.5;
    while($factor>=$dfactor){
        //imagefilter($post_src,post_FILTER_SMOOTH,$dfactor/$factor);
        imagecopyresampled($post_src,$post_src, 0, 0, 0, 0,ceil($srcx/$dfactor),ceil($srcy/$dfactor),$srcx,$srcy);        
        $srcx=ceil($srcx/$dfactor);
        $srcy=ceil($srcy/$dfactor);
        $factor=$srcx/$tarx;    
    }
    $post_tar=imagecreatetruecolor($tarx,$tary);
    imagecopyresampled($post_tar,$post_src, 0, 0, 0, 0,$tarx,$tary,$srcx,$srcy);
    imagedestroy($post_src);
    imagejpeg($post_tar,$thumb,70);
    imagedestroy($post_tar);
    return true;
}

function genpass($length=10,$pronounciable=1,$ucase=1,$nonalpha=0)
{
  $pass='';
  $special=array('i'=>'1','l'=>'!','o'=>'0','s'=>'$','e'=>'3','a'=>'@');
  if($pronounciable){
    $vovels='aeiou';
    $consonants='bcdfghjklmnprstvwyz';
    $state=1;
    while(strlen($pass)<$length||$state!=1){
      $c='';
      if($state<=(mt_rand(0,3)==0?2:1)){
        $c=$consonants[mt_rand(0,18)];
        $state++;
      }else{
        $state=1;
        $c=$vovels[mt_rand(0,4)];
      }
      if($ucase&&$state==2&&mt_rand(0,2)==0)
        $c=strtoupper($c);
      $pass.=$c;
    }
  }else{
    $chrs='abcdefghjkmnpqrstuvwxyz';
    $symbols='!@#$%^&-+123456789';
    while(strlen($pass)<$length)
    {
      $c=$chrs[mt_rand(0,25)];
      if($ucase&&mt_rand(0,4)==0)$c=strtoupper($c);
      elseif($nonalpha&&mt_rand(0,4)==0)$c=$symbols[mt_rand(0,8)];
      $pass.=$c;
    }
  }
  if($nonalpha)
  {
    $i=0;
    $len=strlen($pass);
    while($i<$len){
      if(mt_rand(0,3)==0&&isset($special[$pass[$i]]))
        $pass[$i]=$special[$pass[$i]];
      $i++;
    }
  }
  return $pass;
}
function alphanum($in,$c='_')
{
  return trim(preg_replace('#[^a-z0-9A-Z]+#',$c,$in),$c);
}
function alpha($in,$c='_')
{
  return trim(preg_replace('#[^a-zA-Z]+#',$c,$in),$c);
}
function num($in,$c='')
{
  return trim(preg_replace('#[^0-9]+#',$c,$in),$c);
}
function hsl2rgb($h, $s, $v)
{
  $i = floor($h * 6);
  $f = $h * 6 - $i;
  $p = $v * (1 - $s);
  $q = $v * (1 - $f * $s);
  $t = $v * (1 - (1 - $f) * $s);

  switch($i % 6){
      case 0: $r = $v; $g = $t; $b = $p; break;
      case 1: $r = $q; $g = $v; $b = $p; break;
      case 2: $r = $p; $g = $v; $b = $t; break;
      case 3: $r = $p; $g = $q; $b = $v; break;
      case 4: $r = $t; $g = $p; $b = $v; break;
      case 5: $r = $v; $g = $p; $b = $q; break;
  }

  return array($r,$g,$b);
}
function hsl2hex($h,$s,$l)
{
  return rgb2hex(hsl2rgb($h,$s,$l));
}
function rgb2hex($r,$g=false,$b=false)
{
  if($g===false){
    $g=$r[1];
    $b=$r[2];
    $r=$r[0];
  }
  return sprintf('#%02X%02X%02X',round($r*255),round($g*255),round($b*255));
}

function urlencode_path($in)
{
  $in=rawurlencode($in);
  $in=strtr($in,array('%2F'=>'/'));
  return $in;
}
function truncate($string, $your_desired_width) {
  $parts = preg_split('/([\s\n\r]+)/', $string, null, PREG_SPLIT_DELIM_CAPTURE);
  $parts_count = count($parts);

  $length = 0;
  $last_part = 0;

  $truncated=false;

  for (; $last_part < $parts_count; ++$last_part) {
    $length += strlen($parts[$last_part]);
    if ($length > $your_desired_width) {$truncated=true; break; }
  }
  return implode(array_slice($parts, 0, $last_part)).($truncated?'...':'');
}


function post($url,$params=array(),$timeout=0,$headers=array())
{
  $res=null;
  if($timeout){
  	$id=\ui\cache\data($res,md5('lib_func_post'.$url.serialize($params)),$timeout);
    if($res)
      return $res;
  }
  $ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
//	if(php_uname('n')=='INFIONMOVE'){
//    curl_setopt($ch, CURLOPT_PROXY,'proxy22.iitd.ernet.in:3128');
//  }
  curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
 	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  //to suppress the curl output
	$res = curl_exec($ch);
	curl_close ($ch);
  if($timeout)
    \ui\cache\data_stop($res,$id);
	return $res;
}

function get($url,$params=array(),$timeout=0,$headers=array())
{
  $url.=(strpos($url,'?')==false?'?':'&').http_build_query($params);
  $res=null;
  if($timeout)
  {
    $id=\ui\cache\data($res,md5('lib_func_get'.$url),$timeout);
    if($res)
      return $res;
	}$ch=\curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	//if(php_uname('n')=='INFIONMOVE'){
 //   curl_setopt($ch, CURLOPT_PROXY,'proxy22.iitd.ernet.in:3128');
  //}
	curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  //to suppress the curl output
	$res = curl_exec($ch);
	curl_close ($ch);
  if($timeout)
    \ui\cache\data_stop($res,$id);
	return $res;
}

require('func/password_compat.php');