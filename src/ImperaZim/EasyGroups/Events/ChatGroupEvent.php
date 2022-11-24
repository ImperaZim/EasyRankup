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
  $format = Loader::getProcessedTags(["{tag}", "{name}", "{message}"], [$tag, $name, $message], $chat);
  $event->setFormat($format);
 }

}  
