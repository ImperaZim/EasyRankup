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
  if ($r == null) {
   return "no-plugin";
  }
  $author = $r->getDescription()->getAuthors()[0];
  $authors = explode(" ", $author);
  $author = isset($authors[1]) ? $authors[0] : $author; 
  if ($author == "uTalDoVic") {
   return $r->rank->get($player->getName()) ?? "[+]";
  }
  if ($author == "ImperaZim") {
   return ""; //$r->getRank()->getTag($player)
  }
 }
 
 public function getClan($player) {
  $plugin = Loader::getInstance(); 
  $c = $plugin->getServer()->getPluginManager()->getPlugin("Clan");
  if ($c == null) {
   return "no-plugin";
  }
  $author = $c->getDescription()->getAuthors()[0];
  $authors = explode(" ", $author);
  $author = isset($authors[1]) ? $authors[0] : $author;
  if ($author == "uTalDoVic") {
   return $c->getTag($r->getClan($player)) ?? "no-clan";
  }
  if ($author == "ImperaZim") {
   return ""; //$c->getClan()->getCompactTag($player->getClan())
  }
 }

}  
