<?php
namespace ui\extract;
require 'TermExtractor.php';
require 'DefaultFilter.php';
function process($text){
  $filter = new \DefaultFilter($min_occurrence=5, $keep_if_strength=1);
  $tagger = new \Tagger('english');
  $tagger->initialize($use_apc=true);
  $extractor = new \TermExtractor($tagger, $filter);
  $terms = $extractor->extract($text);
  $res=array();
  foreach ($terms as $term_info) {
      list($term, $occurrence, $word_count) = $term_info;
      $res[]=$term;
  }
  return $res;
}