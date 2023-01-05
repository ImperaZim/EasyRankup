<?php

namespace ImperaZim\EasyGroups\provinders\Provinder;

use pocketmine\player\Player; 

interface Provinder {
 
 public function table();
 
 public function createTable() : void;
 
 public function exist(Player $player) : bool;
 
 public function createProfile(Player $player) : void;
 
 public function getValue(Player $player, String $param);
 
 public function setValue(Player $player, String $param, $value);
 
 public function getAllPermissions() : array;
 
 public function getPermissions(Player $player) : array;
 
 public function setPermission(Player $player, String $permission);
 
 public function unsetPermission(Player $player, String $permission);
 
 public function getPermissionId(Player $player, $permission) : int;
 
}