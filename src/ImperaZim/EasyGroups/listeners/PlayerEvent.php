<?php

namespace ImperaZim\EasyGroups\listeners;

use pocketmine\Server;
use ImperaZim\EasyGroups\EasyGroups;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerChatEvent;

class PlayerEvent implements \pocketmine\event\Listener {
 
 public $plugins = [];
 
 public function __construct() {
  $this->plugins["rankup"] = Server::getInstance()->getPluginManager()->getPlugin("EasyRankup");
 }  

 public function Join(PlayerJoinEvent $event) : void {
  EasyGroups::getInstance()->getProvinder()->createProfile($event->getPlayer());
  EasyGroups::getInstance()->getGroupManager()->getPermissionManager()->updatePermissions($event->getPlayer());
	} 

 public function Chat(PlayerChatEvent $event) : void {
  $player = $event->getPlayer();
  $message = $event->getMessage(); 
  $plugin = EasyGroups::getInstance();
  $group = $plugin->getGroupManager()->getGroup($player);
  
  if(!in_array("easygroups.colored.format", $plugin->getGroupManager()->getPermissionManager()->getPermissions($group))){
   $message = str_replace(["§", "&"], [""], $message);
  }
  
  $tag = $plugin->getGroupManager()->getFormat($group, "tag");
  $format = $plugin->getGroupManager()->getFormat($group, "chat");
  
  $event->setFormat($plugin->getUtils()->replace(["{player}", "{tag}", "{message}", "{rank}"], [$player->getName(), $tag, $message, $this->getRank($player)], $format));
 }
 
 public function getRank($player) {
  if ($this->plugins["rankup"] == null) {
   return "no-plugin";
  }
  return $this->plugins["rankup"]->getRankManager()->getTag($player);
 }  

}

