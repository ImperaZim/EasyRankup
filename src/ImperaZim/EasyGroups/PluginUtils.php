<?php

namespace ImperaZim\EasyGroups;

class PluginUtils {
 
 public function replace($tags, $tostring, $message) : string {
  $message = str_replace(["{prefix}"], [EasyGroups::getInstance()->getMessages()->getNested("plugin.perfix")], $message);
  return str_replace($tags, $tostring, $message);
 }  
 
}
