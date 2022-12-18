<?php

namespace ImperaZim\EasyRankup\Functions\DataBase\Provinder;

use pocketmine\player\Player;
use ImperaZim\EasyRankup\Functions\DataBase\DataBase;

class SQLite3 implements BaseProvinder {
 
 public function table() {
  return new \SQLite3(DataBase::getInstance()->getDataFolder() . "players.db");
 } 
 
 public function createTable() : void {
  $this->table()->exec("CREATE TABLE IF NOT EXISTS profile(name TEXT, rank INT)"); 
 } 
 
 public function exist(Player $player) : bool {
  $data = $this->table()->query("SELECT name FROM profile WHERE name='" . $this->table()->escapeString($player->getName()) . "';");
  return isset($data->fetchArray(SQLITE3_ASSOC)['name']);
 } 
 
 public function createProfile(Player $player) : void {
  $perfil = $this->table()->prepare("INSERT INTO profile(name, rank) VALUES (:name, :rank)");
  $perfil->bindValue(":name", $player->getName()); 
  $perfil->bindValue(":rank", 0);
  if (!$this->exist($player)) {
   $perfil->execute();
  }  
 }
 
 public function addParamToRank(Player $player, Int $value) : bool {
  if ($this->exist($player)) {
   $this->table()->query("UPDATE profile SET rank=rank+". $value." WHERE name='" . $this->table()->escapeString($player->getName()) . "';");
   return true;
  }
  return false;
 }
 
 public function getRankId(Player $player) {
  if ($this->exist($player)) {
   $data = $this->table()->query("SELECT * FROM profile WHERE name='" . $this->table()->escapeString($player->getName()) . "';");
   return $data->fetchArray(SQLITE3_ASSOC)['rank'];
  }
  return 0; 
 } 
 
}