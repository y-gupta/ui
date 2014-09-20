<?php
namespace ui\shortcode;
function process($content){
  if(!\ui\global_var('shortcode_tags_regx'))
    return $content;
  $pattern = '/\[(\[?)(' . \ui\global_var('shortcode_tags_regx') . ')(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)/';
  return preg_replace_callback($pattern,'\\ui\\shortcode\\exec_tag', $content);
}
function exec_tag($tag){
  if ( $tag[1] == '[' && $tag[6] == ']' ) {
   return substr($tag[0], 1, -1);
  }
  $param=array(parse_atts($tag[3]),$tag[5]);
  return $tag[1].\ui\execute_hook('shortcode_tag_'.$tag[2],$param).$tag[6];
}
function register($tag,$func){
  $tags=&\ui\global_var('shortcode_tags',array());
  $tags[]=$tag;
  \ui\global_var('shortcode_tags_regx',join('|',array_map('preg_quote',$tags)),1);
  \ui\register_hook('shortcode_tag_'.$tag,$func);
}
function parse_atts($text) {
  $atts = array();
  $pattern = '/(\w+)\s*=\s*"([^"]*)"(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';
  $text = preg_replace("/[\x{00a0}\x{200b}]+/u", " ", $text);
  if ( preg_match_all($pattern, $text, $match, PREG_SET_ORDER) ) {
    foreach ($match as $m) {
      if (!empty($m[1]))
        $atts[strtolower($m[1])] = stripcslashes($m[2]);
      elseif (!empty($m[3]))
        $atts[strtolower($m[3])] = stripcslashes($m[4]);
      elseif (!empty($m[5]))
        $atts[strtolower($m[5])] = stripcslashes($m[6]);
      elseif (isset($m[7]) and strlen($m[7]))
        $atts[] = stripcslashes($m[7]);
      elseif (isset($m[8]))
        $atts[] = stripcslashes($m[8]);
    }
  } else {
    $atts = ltrim($text);
  }
  return $atts;
}
