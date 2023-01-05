<?php

namespace ImperaZim\EasyGroups\tasks;

use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\scheduler\Task;
use ImperaZim\EasyGroups\EasyGroups;

class AsyncTask extends Task { 
 
 public $plugins = [];
 
 public function __construct() {
  $this->plugins["rankup"] = Server::getInstance()->getPluginManager()->getPlugin("EasyRankup");
 }  
 
 public static function register($plugin, AsyncTask $task) : void {
  $plugin->getScheduler()->scheduleRepeatingTask($task, 20);
 } 
 
 public function onRun() : void {
  $server = Server::getInstance();
  $plugin = EasyGroups::getInstance();
  
  $manager = $plugin->getGroupManager();
  
  foreach ($server->getOnlinePlayers() as $player) {
   if ($manager->isValid($player)) {
    $player->sendMessage($plugin->getUtils()->replace(["{group}"], [$manager->getFormat($manager->getDefault(), "tag")], $plugin->getMessages()->getNested("messages.update.RESET_TO_DEFAULT")));
   }else{
    $group = $manager->getGroup($player);
    $tag = $manager->getFormat($group, "tag");
    $format = $manager->getFormat($group, "nametag");
    
    if ($manager->timeout($player)) {
     $player->sendMessage($plugin->getUtils()->replace(["{group}"], [$manager->getFormat($manager->getDefault(), "tag")], $plugin->getMessages()->getNested("messages.update.GROUP_TIME_OUT")));
    }
    
    $player->setNameTag($plugin->getUtils()->replace(["{tag}", "{player}", "{rank}"], [$tag, $player->getName(), $this->getRank($player)], $format));
   }
  }
  
  if (!isset($manager->getAll()[$manager->getDefault()])) {
   //$manager->generatePath();
  }
  
 }
 
 public function getRank($player) {
  if ($this->plugins["rankup"] == null) {
   return "no-plugin";
  }
  return $this->plugins["rankup"]->getRankManager()->getTag($player);
 } 
 
}
