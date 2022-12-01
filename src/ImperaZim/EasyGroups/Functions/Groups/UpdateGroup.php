<?php

namespace ImperaZim\EasyGroups\Functions\Groups;

use pocketmine\Server; 
use pocketmine\utils\Config; 
use ImperaZim\EasyGroups\Loader;
use ImperaZim\EasyGroups\Functions\Storage\SQLite3; 
use ImperaZim\EasyGroups\Functions\Permission\UpdatePermissions;  

class UpdateGroup {

 public static function execute($author, $player, String $group) : void {
   self::update($author, $player, $group);
  }
  
 public static function update($author, $player, String $group) : void {
  $name = $player->getName();
  $plugin = Loader::getInstance();
  $messagem = $plugin->getConfig(); 
  $config = new Config($plugin->getDataFolder() . "groups.yml");
  if (isset($config->getAll()[$group])) {
   SQLite3::table()->query("UPDATE profile SET tag=".$group." WHERE name=".$name.";");
   $tag = $config->getAll()[$group]["tag"];
   $author->sendMessage(Loader::getProcessedTags(["{prefix}", "{target}", "{group}"], [$plugin->getConfig()->get("default.prefix"), $name, $tag], $messagem->getNested('commands.subcommands.setcommand.author_sucess', false))); 
   $player->sendMessage(Loader::getProcessedTags(["{prefix}", "{group}"], [$plugin->getConfig()->get("default.prefix"), $tag], $messagem->getNested('commands.subcommands.setcommand.target_sucess', false))); 
   $player->addAttachment($plugin)->clearPermissions(); 
   UpdatePermissions::execute($player); 
   if($config->getAll()[$group]["type"] == "vip") { 
    self::notify($player, $group);
   } 
  }else{
   $player->sendMessage(Loader::getProcessedTags(["{prefix}", "{group}"], [$plugin->getConfig()->get("default.prefix"), $group], $messagem->getNested('commands.subcommands.setcommand.unknow_group', false))); 
  }
   
 } 
 
 public static function notify($player, String $group) : void {
  $plugin = Loader::getInstance();
  $config = new Config($plugin->getDataFolder() . "groups.yml");
  foreach (Server::getInstance()->getOnlinePlayers() as $players) {
   $players->sendTitle("{$config->getAll()[$group]['tag']}");
   $players->sendSubtitle("{$player->getName()} tornou-se");
  }
 }


} 
