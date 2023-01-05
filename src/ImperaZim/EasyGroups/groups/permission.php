<?php

namespace ImperaZim\EasyGroups\groups;

use pocketmine\player\Player;
use ImperaZim\EasyGroups\EasyGroups;

class permission extends group {
 
 public function getPermissions(String $group) : array {
  return $this->getAll()[$group]["permissions"] ?? [];
 }
 
 public function setPermission(String $group, $permission) : bool {
  $data = $this->getConfig();
  if (isset($data->getAll()[$group])) {
   $config = $data->getAll(); 
   if (!in_array($permission, $this->getPermissions($group))) {
    array_push($config[$group]["permissions"], $permission); 
    $data->setAll($config); $data->save();
    $this->recalPermissions(); 
    return true;
   }
  }
  return false;
 }
 
 public function unsetPermission(String $group, $permission) : bool {
  $data = $this->getConfig();
  if (isset($data->getAll()[$group])) {
   $config = $data->getAll();  
   if (!in_array($permission, $this->getPermissions($group))) {
    $permissionId = $this->getPermissionId($group, $permission);
    unset($config[$group]["permissions"][$permissionId]); 
    $data->setAll($config); $data->save();
    $this->recalPermissions();
    return true;
   }
  } 
  return false;
 }
 
 public function getPermissionId(String $group, $permission) : int {
  $permissionId = 0;
  foreach ($this->getPermissions($group) as $permissions) {
   if ($permissions == $permission) return $permissionId;
   $permissionId = $permissionId + 1;
  }
 }
 
 public function recalPermissions() : void {
  $plugin = EasyGroups::getInstance();
  foreach ($plugin->getServer()->getOnlinePlayers() as $player) {
   $this->updatePermissions($player);
  }
  $plugin->getUserManager()->recalPermissions();
 }
 
 public function updatePermissions(Player $player) : void {
  $plugin = EasyGroups::getInstance();
  foreach ($this->getAll() as $group => $data) {
   foreach ($this->getPermissions($group) as $permission) {
    $player->addAttachment($plugin)->setPermission($permission, false);
   }
  }
  foreach ($this->getPermissions($this->getGroup($player)) as $permission) {
   $player->addAttachment($plugin)->setPermission($permission, true);
  } 
  $plugin->getUserManager()->updatePermissions($player); 
 }
 
}
