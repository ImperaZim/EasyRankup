<?php

namespace ImperaZim\EasyGroups\Events;

use pocketmine\utils\Config;
use pocketmine\event\Listener;
use ImperaZim\EasyGroups\Loader;  
use pocketmine\event\player\PlayerChatEvent;
use ImperaZim\EasyGroups\Functions\Groups\_Group;  

class ChatGroupEvent implements Listener {
 
 public function __construct() {
  $clan = Loader::getInstance()->getServer()->getPluginManager()->getPlugin("Clan");
  $rankup = Loader::getInstance()->getServer()->getPluginManager()->getPlugin("RankUP");
  $this->clan = $clan; 
  $this->rankup = $rankup; 
 }

 public function Event(PlayerChatEvent $event) {
  $player = $event->getPlayer();
  $message = $event->getMessage();
  if(!$player->hasPermission("easygroups.colored.format")){
   $colors = ["§", "&"];
   $message = str_replace($colors, [""], $message);
  }
  $plugin = Loader::getInstance();
  $config = new Config($plugin->getDataFolder() . "groups.yml"); 
  $name = $player->getName();
  $group = _Group::get($player);
  $tag = $config->getAll()[$group]["tag"] ?? "[]";
  $chat = $config->getAll()[$group]["chat"] ?? "{tag} {name}: {message}";
  $clan = $this->getClan($player);
  $rank = $this->getRank($player);
  $format = Loader::getProcessedTags(["{tag}", "{name}", "{message}", "{clan}", "{rank}"], [$tag, $name, $message, $clan, $rank], $chat);
  $event->setFormat($format);
 }
 
 public function getRank($player) {
  if ($this->rankup == null) {
   return "no-plugin";
  }
  return $this->rankup->rank->get($player->getName()) ?? "[+]";
 }
 
 public function getClan($player) {
  if ($this->clan == null) {
   return "no-plugin";
  }
  return $this->clan->getTag($this->clan->getClan($player)) ?? "no-clan";
 } 

}  
