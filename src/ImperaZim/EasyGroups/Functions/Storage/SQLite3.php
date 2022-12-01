<?php

namespace ImperaZim\EasyGroups\Functions\Storage;

use pocketmine\player\Player;
use ImperaZim\EasyGroups\Loader; 

class SQLite3 {
 
 public static function default() : String {
 return Loader::getInstance()->getConfig()->get("default.group");
  
 }
 
 public static function table() {
  return new \SQLite3(Loader::getInstance()->getDataFolder() . "players.db");
 }
  
 public static function createTable() : void {
  self::table()->exec("CREATE TABLE IF NOT EXISTS profile(name TEXT, tag TEXT)"); 
 }
 
 public static function createProfile(Player $player) : void {
  $default = self::default();
  $name = $player->getName();
  $data = self::table();
  $loader = Loader::getInstance();
  $perfil = $data->prepare("INSERT INTO profile(name, tag) VALUES (':name', ':tag')");
  $perfil->bindValue(":name", $name); 
  $perfil->bindValue(":tag", $default);
  if (!self::exist($player)) {
   $perfil->execute();
   $loader->getLogger()->notice("New player \"{$player->getName()}\" group status as \"{$default}\"");
  }  
 }
 
 public static function exist(Player $player) {
  $data = self::table();
  $loader = Loader::getInstance();
  $data = $data->query("SELECT name FROM profile WHERE name='" . $player->getName() . "'");
  $data = $data->fetchArray(SQLITE3_ASSOC);
  if(isset($data['name'])) return true;
  return false;
 } 
 
}
