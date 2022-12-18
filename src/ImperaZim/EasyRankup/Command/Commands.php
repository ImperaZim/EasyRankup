<?php

namespace ImperaZim\EasyRankup\Command;

class Commands extends \pocketmine\Server {
 
 public static function registerAll() : void {
   $commands = [
    "Ranks" => new Rank\RanksCommand(), 
    "Rankup" => new Rank\RankUPCommand(), 
   ];
   foreach ($commands as $name => $command) {
    self::getInstance()->getCommandMap()->register($name, $command);
   }
  } 
  
} 
 
