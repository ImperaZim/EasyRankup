<?php

namespace ImperaZim\EasyRankup\Command\Rank;

use pocketmine\Server;
use pocketmine\player\Player;
use pocketmine\command\Command;
use pocketmine\plugin\PluginOwned;
use ImperaZim\EasyRankup\EasyRankup;
use ImperaZim\EasyRankup\PluginUtils;
use pocketmine\command\CommandSender;
use ImperaZim\EasyRankup\Task\RankUPTask;

class RankUPCommand extends Command implements PluginOwned {

 public function __construct() {
  parent::__construct("rankup", "§7rankup command!", null, []);
 }

 public function execute(CommandSender $player, String $commandLabel, array $args) : bool {
  $plugin = $this->getOwningPlugin();
  if (!$player instanceof Player) {
   $plugin->getLogger()->error("This command can only be used in the game"); 
   return true;
  }
  
  if (isset($plugin->tasks[$player->getName()])) {
    $player->sendMessage(PluginUtils::convertString([], [], $plugin->getMessages()->getNested("commands.rankup.notify.duplicate")));
    return true;
   }
  
  $plugin->tasks[$player->getName()] = $player->getName(); 
   
  if ($plugin->getRankManager()->isLastRank($player)) {
   $player->sendMessage(PluginUtils::convertString([], [], $plugin->getMessages()->getNested("commands.rankup.notify.in_last_rank")));
   return true;
  } 
  $plugin->getScheduler()->scheduleRepeatingTask(new RankUPTask($player, 1), 20); 
  return true;
 } 
 
 public function getOwningPlugin() : EasyRankup {
  return EasyRankup::getInstance();
 } 
 
} 
