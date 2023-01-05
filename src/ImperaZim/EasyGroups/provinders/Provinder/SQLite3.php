<?php

namespace ImperaZim\EasyGroups\provinders\Provinder;

use pocketmine\player\Player;
use ImperaZim\EasyGroups\provinders\DataBase;

class SQLite3 implements Provinder {
 
 public function table() {
  return new \SQLite3(DataBase::getInstance()->getDataFolder() . "players.db");
 } 
 
 public function createTable() : void {
  $this->table()->query("CREATE TABLE IF NOT EXISTS players(name TEXT, tag TEXT, time INT, permissions TEXT)"); 
 } 
 
 public function exist(Player $player) : bool {
  $data = $this->table()->query("SELECT * FROM players WHERE name='" . $this->table()->escapeString($player->getName()) . "';");
  return isset($data->fetchArray(SQLITE3_ASSOC)['name']);
 } 
 
 public function createProfile(Player $player) : void {
  $perfil = $this->table()->prepare("INSERT INTO players(name, tag, time, permissions) VALUES (:name, :tag, :time, :permissions)");
  $perfil->bindValue(':name', $player->getName()); 
  $perfil->bindValue(':tag', DataBase::getDefault()); 
  $perfil->bindValue(':time', 0);
  $perfil->bindValue(':permissions', '');
  if (!$this->exist($player)) {
   $perfil->execute();
  }  
 }
 
 public function getValue(Player $player, String $param) {
  if ($this->exist($player)) {
   $data = $this->table()->query("SELECT * FROM players WHERE name='" . $this->table()->escapeString($player->getName()) . "';");
   return $data->fetchArray(SQLITE3_ASSOC)[$param] ?? ""; 
  }
  return "";
 }
 
 public function getTag(Player $player) : string {
  $data = $this->table()->query("SELECT * FROM players WHERE name='" . $this->table()->escapeString($player->getName()) . "';");
   return isset($data->fetchArray(SQLITE3_ASSOC)[$param]);  
 }
 
 public function setValue(Player $player, String $param, $value) {
  if ($this->exist($player)) {
   $this->table()->query("UPDATE players SET '$param'='$value' WHERE name = '" . $this->table()->escapeString($player->getName()) . "';");
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
  $count = 0;
  $tab = array();
  $permissions_array = [];
  $query = $this->table()->query("SELECT * FROM players;");
  while ($get = $query->fetchArray(SQLITE3_ASSOC)) {
   $to = $count + 1;
   $tab[$count]['permissions'] = $get['permissions'];
   $permission_string = $tab[$count]['permissions'];
		 $permissions = [$tab[$count]['permissions']];
		 if (str_contains($permission_string, ':')) {
		  $permissions = explode(":", $permission_string);
		 } 
   foreach ($permissions as $permission) {
		  if (!in_array($permission, ["", " "])) {
		  	array_push($permissions_array, $permission);
		  }
		 }
   $count = $count + 1;
  } 
  return $permissions_array;
 }  
 
}