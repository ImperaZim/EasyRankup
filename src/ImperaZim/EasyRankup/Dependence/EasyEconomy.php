<?php

namespace ImperaZim\EasyRankup\Dependence;

use pocketmine\player\Player; 

class EasyEconomy extends \pocketmine\Server {
 
 public static function getMoney(Player $player) {
  $manager = self::getInstance()->getPluginManager();
  if ($manager->getPlugin("EasyEconomy") == null) return 0;
  return $manager->getPlugin("EasyEconomy")->getProvinder()->getMoney($player);
 }
 
 public static function reduceMoney(Player $player, Int $value) {
  $manager = self::getInstance()->getPluginManager();
  if ($manager->getPlugin("EasyEconomy") == null) return;
  return $manager->getPlugin("EasyEconomy")->getProvinder()->reduceMoney($player, $value);
 }
 
}
