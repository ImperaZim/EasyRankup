<?php

namespace ImperaZim\EasyGroups\user;

use pocketmine\player\Player;
use ImperaZim\EasyGroups\EasyGroups;

class permission {
 
 public function getPermissions(Player $player) : array {
  return EasyGroups::getInstance()->getProvinder()->getPermissions($player) ?? [];
 }
 
 public function setPermission(Player $player, $permission) {
  EasyGroups::getInstance()->getProvinder()->setPermission($player, $permission);
  $this->recalPermissions();
 }
 
 public function unsetPermission(Player $player, $permission) {
  EasyGroups::getInstance()->getProvinder()->unsetPermission($player, $permission); 
  $this->recalPermissions();
 }
 
 public function getPermissionId(String $group, $permission) : int {
  EasyGroups::getInstance()->getProvinder()->getPermissionId($player, $permission);
 }
 
 public function recalPermissions() : void {
  $plugin = EasyGroups::getInstance();
  $permissions = $plugin->getProvinder()->getAllPermissions();
  foreach ($plugin->getServer()->getOnlinePlayers() as $player) {
   $this->updatePermissions($player);
  }
 }
 
 public function updatePermissions(Player $player) : void {
  $plugin = EasyGroups::getInstance();
  $permissions = $plugin->getProvinder()->getAllPermissions();
  foreach ($permissions as $permission) {
   $player->addAttachment($plugin)->setPermission($permission, false);
  }
  foreach ($this->getPermissions($player) as $permission) {
   $player->addAttachment($plugin)->setPermission($permission, true);
  }
 }
 
} 
