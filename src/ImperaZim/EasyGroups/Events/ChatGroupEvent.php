<?php

namespace ImperaZim\EasyGroups\Events;

use pocketmine\utils\Config;
use pocketmine\event\Listener;
use ImperaZim\EasyGroups\Loader;  
use pocketmine\event\player\PlayerChatEvent;
use ImperaZim\EasyGroups\Functions\Groups\_Group;  

class ChatGroupEvent implements Listener {

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
  $plugin = Loader::getInstance(); 
  $r = $plugin->getServer()->getPluginManager()->getPlugin("RankUP");
  if ($r != null) {
   if ($r->getDescription()->getAuthors()[0] == "uTalDoVic") {
    return $r->rank->get($player->getName()) ?? "[+]";
   }
   if ($r->getDescription()->getAuthors()[0] == "ImperaZim") {
    return "";
   }
  }
  return "no-plugin";
 }
 
 public function getClan($player) {
  $plugin = Loader::getInstance(); 
  $c = $plugin->getServer()->getPluginManager()->getPlugin("Clan");
  if ($c == null) {
   if ($c->getDescription()->getAuthors()[0] == "uTalDoVic") {
    return $c->getTag($c->getClan($player)) ?? "no-clan";
   }
   if ($r->getDescription()->getAuthors()[0] == "ImperaZim") {
    return "";
   }
  }
  return "no-plugin";
 } 

}  
