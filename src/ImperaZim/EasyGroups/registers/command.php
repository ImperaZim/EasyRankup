<?php

namespace ImperaZim\EasyGroups\registers;

use ImperaZim\EasyGroups\commands\GroupCommand;

class command extends \pocketmine\Server {
 
 public static function registerAll() : void {
   $commands = [
    "group" => new GroupCommand(), 
   ];
   foreach ($commands as $name => $command) {
    self::getInstance()->getCommandMap()->register($name, $command);
   }
  } 
  
} 
 
