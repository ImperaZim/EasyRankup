<?php

namespace ImperaZim\EasyGroups\provinders\Provinder;

use pocketmine\utils\Config;
use pocketmine\player\Player;
use ImperaZim\EasyGroups\provinders\DataBase;

class YAML implements Provinder { 
 
 public function table() {
  return new Config(DataBase::getInstance()->getDataFolder() . "players.yml");
 } 
 
 public function createTable() : void {
  (new Config(DataBase::getInstance()->getDataFolder() . "players.yml", Config::YAML, ["players" => []]))->save();
 } 
 
 public function exist(Player $player) : bool {
  return isset($this->table()->getAll()["players"][$player->getName()]);
 } 
 
 public function createProfile(Player $player) : void {
  $perfil = (new Config(DataBase::getInstance()->getDataFolder() . "players.yml", Config::YAML, [
   "players" => [
    $player->getName() => [
     "tag" => DataBase::getDefault(), 
     "time" => 0,
     "permissions" => []
    ]
   ]]));
  if (!$this->exist($player)) {
   $perfil->save();
  }  
 } 
 
 public function getValue(Player $player, String $param) {
  if ($this->exist($player)) {
   return $this->table()->getNested("players.{$player->getName()}.$param");
  }
  return DataBase::getInstance()->getGroupManager()->getDefault();
 }
 
 public function setValue(Player $player, String $param, $value) {
  if ($this->exist($player)) {
   $config = $this->table();
   $config->setNested("players.{$player->getName()}.$param", $value);
   $config->save();  
  }
 }
 
 public function getPermissions(Player $player) : array {
  return $this->table()->getNested("players.{$player->getName()}.permissions", []); 
 } 
 
 public function setPermission(Player $player, String $permission) {
  $data = $this->table(); 
  $config = $data->getAll();
  $player = $player->getName();
  array_push($config["players"][$player]["permissions"], $permission); 
  $data->setAll($config); 
  $data->save(); 
 }
 
 public function unsetPermission(Player $player, String $permission) {
  $data = $this->table(); 
  $config = $data->getAll();
  $player = $player->getName(); 
  if (!in_array($permission, $this->getPermissions($player))) {
   $permissionId = $this->getPermissionId($player, $permission);
   unset($config["players"][$player]["permissions"][$permissionId]); 
   $data->setAll($config);
   $data->save();
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
  $plugin = DataBase::getInstance();
  foreach ($this->table()->getAll()["players"] as $player => $data) {
   foreach ($data["permissions"] as $permission){
    array_push($permissions_array, $permission);
   }
  }
  return $permissions_array;
 } 
 
}