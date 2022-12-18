<?php

namespace ImperaZim\EasyRankup\Dependence;

use pocketmine\player\Player; 
use ImperaZim\EasyRankup\EasyRankup;

class EasyEconomy {
 
 public static $economy = null;
 
 public function __construct() {
  $economy = EasyRankup::getInstance()->getServer()->getPluginManager()->getPlugin("EasyEconomy");
  self::$economy = $economy; 
 } 
 
 public static function getMoney(Player $player) {
  if (self::$economy == null) return 0;
  return self::$economy->getProvinder()->getMoney($player);
 }
 
 public static function reduceMoney(Player $player, Int $value) {
  if (self::$economy == null) return;
  return self::$economy->getProvinder()->reduceMoney($player, $value);
 }
 
}
