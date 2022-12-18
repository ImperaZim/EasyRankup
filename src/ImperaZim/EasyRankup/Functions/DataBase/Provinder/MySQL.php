<?php

namespace ImperaZim\EasyRankup\Functions\DataBase\Provinder;

use pocketmine\player\Player;
use ImperaZim\EasyRankup\Functions\DataBase\DataBase;

class MySQL implements BaseProvinder {
 
 public function table() {
  $config = DataBase::getInstance()->getConfig()->get("database-provider", []);
		return new \mysqli(
			$config["host"] ?? "51.81.47.131",
			$config["user"] ?? "u720_m5HkgVNSm8",
			$config["password"] ?? "dZJBsc=3Fno@OsWpY3dB+zpp",
			$config["db"] ?? "s720_EasyRankup",
			$config["port"] ?? 3306);
 }
  
 public function createTable() : void {
  $this->table()->query("CREATE TABLE IF NOT EXISTS profile(name TEXT, money INT)"); 
 }
 
 public function exist(Player $player) : bool {
 	$result = $this->table()->query("SELECT * FROM profile WHERE name='". $this->table()->real_escape_string($player->getName()) . "';");
 	return $result->num_rows > 0 ? true : false;
 } 
 
 public function createProfile(Player $player) : void {
  if (!$this->exist($player)) {
   $perfil = $this->table()->prepare("INSERT INTO profile(name, money) VALUES ('" . $this->table()->real_escape_string($player->getName()) . "', 0)");
  }  
 }
 
 public function addParamToRank(Player $player, Int $value) : bool {
  if ($this->exist($player)) {
   $this->table()->query("UPDATE profile SET rank=rank+". $value ." WHERE name='".$this->table()->real_escape_string($player->getName())."';");
   return true;
  }
  return false; 
 } 
 
 public function getRankId(Player $player) {
  if ($this->exist($player)) {
   $res = $this->table()->query("SELECT rank FROM profile WHERE name='".$this->table()->real_escape_string($player->getName())."'");
 		$ret = $res->fetch_array()[0] ?? 0;
 		$res->free();
 		return $ret; 
  }
  return 0; 
 } 
 
}