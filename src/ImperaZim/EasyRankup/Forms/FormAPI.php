<?php

namespace ImperaZim\EasyRankup\Forms;

use pocketmine\plugin\PluginBase;

class FormAPI {

 public static function createSimpleForm(?callable $function = null) {
  return new SimpleForm($function);
 }

} 
 