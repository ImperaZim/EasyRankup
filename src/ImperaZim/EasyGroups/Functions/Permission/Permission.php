<?php

namespace ImperaZim\EasyGroups\Functions\Permission;

use pocketmine\utils\Config;
use ImperaZim\EasyGroups\Loader;
use pocketmine\permissions\DefaultPermissionNames;

class Permission {
 
 const CORE_PERM = "\x70\x70\x65\x72\x6d\x73\x2e\x63\x6f\x6d\x6d\x61\x6e\x64\x2e\x70\x70\x69\x6e\x66\x6f"; 
 const USER_PERM = "pocketmine.group.user";
 const OPERATOR_PERM = "pocketmine.group.operator";
 const USER_BROADCAST = "pocketmine.broadcast.user";
 const OPERATOR_BROADCAST = "pocketmine.broadcast.admin";
 
 public static function getPermissionsByGroup($group) {
  $plugin = Loader::getInstance();
  $config = new Config($plugin->getDataFolder() . "groups.yml");
  $permission = $config->getAll()[$group]["permission"];
  return $permission;
 } 
 
} 
