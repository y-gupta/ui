<?php
namespace ui\aws;
function s3_url($file,$bucket,$expires=0)//if $expires=0, it is set to 24 hrs after time();
{
  $file=rawurlencode($file);
  $file=str_replace('%2F', '/', $file);
  $path=$bucket.'/'.$file;
  if($expires===0)
    $expires=time()+24*3600;  
  $str = utf8_encode("GET\n\n\n$expires\n//$path");
  $str = hash_hmac('sha1',$str,\ui\config('aws_secret'), true);
  $str = base64_encode($str);
  $str = urlencode($str);
  $url = "http://$bucket.s3.amazonaws.com/$file";
  $url.= '?AWSAccessKeyId='.\ui\config('aws_id').'&Expires='.$expires.'&Signature='.$str;
  return $url;
}