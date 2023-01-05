<?php

namespace ImperaZim\EasyGroups\provinders\Provinder;

use pocketmine\player\Player;
use ImperaZim\EasyGroups\provinders\DataBase;

class MySQL implements Provinder {
 
 public function table() {
  $config = DataBase::getInstance()->getConfig()->getNested("database.mysql-provider"); //, []
		return new \mysqli($config["host"], $config["user"], $config["password"], $config["db"], $config["port"]);
 }
  
 public function createTable() : void {
  $this->table()->query("CREATE TABLE IF NOT EXISTS players(name TEXT, tag TEXT, time INT, permissions TEXT)"); 
 } 
 
 public function createProfile(Player $player) : void {
  if (!$this->exist($player)) {
   $name = $player->getName();
   $group = DataBase::getDefault();
   $perfil = $this->table()->query("INSERT INTO players(name, tag, time, permissions) VALUES ('$name', '$group', 0, '');");
   $perfil = $this->table()->prepare("INSERT INTO players(name, tag, time, permissions) VALUES ('$name', '$group', 0, '');");
  }  
 } 
 
 public function exist(Player $player) : bool {
  $name = $player->getName();
 	$result = $this->table()->query("SELECT * FROM players WHERE name='$name';");
 	return $result->num_rows > 0 ? true : false;
 } 
 
 public function getValue(Player $player, String $param) {
  if ($this->exist($player)) {
   $res = $this->table()->query("SELECT '$param' FROM players WHERE name='".$this->table()->real_escape_string($player->getName())."'");
 		$ret = $res->fetch_array()[0];
 		$res->free();
 		return $ret;  
  }
  return "0";
 }
 
 public function setValue(Player $player, String $param, $value) {
  if ($this->exist($player)) {
   $this->table()->query("UPDATE players SET '$param'='$value' WHERE name = '" . $this->table()->real_escape_string($player->getName()) . "'");
  }
 }
 
 public function getPermissions(Player $player) : array {
  $permissions_array = [];
  $permissions_string = $this->getValue($player, "permissions");
  $permissions_explode = explode(":", $permissions_string . ":");
  foreach ($permissions_explode as $permission) {
   if ($permission != "" || $permission != " ") {
    array_push($permissions_array, $permission);
   }
  }
  return $permissions_array;
 } 
 
 public function setPermission(Player $player, String $permission) {
  $permissions = $this->getPermissions($player);
  array_push($permissions, $permission);
  $permissions_string = "";
  foreach ($permissions as $permission) {
   if ($permissions_string != "") $permissions_string .= ":{$permission}";
   if ($permissions_string == "") $permissions_string .= "{$permission}";
  }
  $this->setValue($player, "permissions", $permissions_string);
 } 
 
 public function unsetPermission(Player $player, String $permission) {
  $permissions = $this->getPermissions($player);
  if (in_array($permission, $permissions)) {
   $permissionId = $this->getPermissionId($player, $permission);
   unset($permissions[$permissionId]);
   $permissions_string = "";
   foreach ($permissions as $permission) {
    if ($permissions_string != "") $permissions_string .= ":{$permission}";
    if ($permissions_string == "") $permissions_string .= "{$permission}";
   }
   $this->setValue($player, "permissions", $permissions_string);
  }
 }
 
 public function getPermissionId(Player $player, $permission) : int {
  $permissionId = 0;
  foreach ($this->getPermissions($player) as $permissions) {
   if ($permissions == $permission) return $permissionId;
   $permissionId = $permissionId + 1;
  }
 } 
 
 public function getAllPermissions() : array {
  $permissions_array = [];
  $query = $this->table()->query("SELECT * FROM players;");
		foreach($query->fetch_all() as $val) {
		 $permission_string = $val[3];
		 $permissions = ["$val[3]"];
		 if (str_contains($permission_string, ':')) {
		  $permissions = explode(":", $permission_string);
		 }
		 foreach ($permissions as $permission) {
		  if (!in_array($permission, ["", " "])) {
		  	array_push($permissions_array, $permission);
		  }
		 }
		}
		$query->free();
  return $permissions_array;
 } 
 
}