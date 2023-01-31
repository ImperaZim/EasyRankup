<?php

namespace ImperaZim\EasyRankup\Dependence;

use pocketmine\player\Player;
use ImperaZim\EasyRankup\EasyRankup;
use onebone\economyapi\EconomyAPI as API;
use ImperaZim\EasyEconomy\EasyEconomy as EE;
use cooldogedev\BedrockEconomy\api\BedrockEconomy as BE;

class Economy extends \pocketmine\Server {

 public static function hasDependences() : bool {
  $x01 = 0;
  $manager = self::getInstance()->getPluginManager();
  if ($manager->getPlugin("EconomyAPI") != null) $x01 = 1;
  if ($manager->getPlugin("EasyEconomy") != null) $x01 = 1;
  if ($manager->getPlugin("BedrockEconomy") != null) $x01 = 1;
  if ($x01 == 1) $x01 = self::check() ? 1 : 0;
  return $x01 == 1;
 }

 public static function check() : bool {
  $dependence = EasyRankup::getInstance()->getConfig()->getNested("economy-type");
  return in_array(strtolower(($dependence)), ["easyeconomy", "economyapi", "bedrockeconomy"]);
 }

 public static function getMoney($player) : Int {
  $dependence = EasyRankup::getInstance()->getConfig()->getNested("economy-type");
  if (strtolower(($dependence)) == "economyapi") {
   return API::getInstance()->myMoney($player->getName());
  }
  if (strtolower(($dependence)) == "easyeconomy") {
   return EE::getInstance()->getProvinder()->getMoney($player);
  }
  if (strtolower(($dependence)) == "bedrockeconomy") {
   return BE::legacy()->getPlayerBalance(
    $player->getName(),
    ClosureContext::create(
     function (?int $balance): void {
      var_dump($balance);
     },
    )
   );
  }
  return 0;
 }

 public static function reduceMoney($player, $value) : void {
  $dependence = EasyRankup::getInstance()->getConfig()->getNested("economy-type");
  if (strtolower(($dependence)) == "economyapi") {
   API::getInstance()->reduceMoney($player->getName(), $value);
  }
  if (strtolower(($dependence)) == "easyeconomy") {
   EE::getInstance()->getProvinder()->reduceMoney($player, $value);
  }
  if (strtolower(($dependence)) == "bedrockeconomy") {
   BE::legacy()->subtractFromPlayerBalance(
    $player->getName(),
    $value,
    ClosureContext::create(
     function (bool $wasUpdated): void {
      var_dump($wasUpdated);
     },
    )
   );
  }
 }

}
