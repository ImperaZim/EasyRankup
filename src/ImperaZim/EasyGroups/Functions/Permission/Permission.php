<?php

namespace ImperaZim\EasyGroups\Functions\Permission;

use pocketmine\utils\Config;
use ImperaZim\EasyGroups\Loader;
use pocketmine\permissions\DefaultPermissionNames;

class Permission {
 
 const CORE_PERM = "pocketmine"; 
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
