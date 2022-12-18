<?php

namespace ImperaZim\EasyRankup\Dependence;

use pocketmine\player\Player; 
use ImperaZim\EasyRankup\EasyRankup;

class EasyEconomy {
 
 public $economy = null;
 
 public function __construct() {
  $economy = Loader::getInstance()->getServer()->getPluginManager()->getPlugin("EasyEconomy");
  $this->economy = $economy; 
 } 
 
 public static function getMoney(Player $player) {
  if ($this->economy == null) return 0;
  return $this->economy->getProvinder()->getMoney($player);
 }
 
 public static function reduceMoney(Player $player, Int $value) {
  if ($this->economy == null) return;
  return $this->economy->getProvinder()->reduceMoney($player, $value);
 }
 
}
