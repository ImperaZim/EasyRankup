<?php

namespace ImperaZim\EasyRankup;

class PluginUtils extends EasyRankup {
 
 public static function convertString($tags, $processeds, $message) {
  $message = str_replace(["{prefix}"], [self::$instance->getMessages()->getNested("prefix")], $message);
  return str_replace($tags, $processeds, $message);
 }  
 
} 
