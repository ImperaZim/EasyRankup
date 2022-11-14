<?php

namespace ImperaZim\EasyGroups\Functions\Permission;

use pocketmine\Server;
use pocketmine\utils\Config;
use ImperaZim\EasyGroups\Loader;
use ImperaZim\EasyGroups\Functions\Groups\_Group;
use ImperaZim\EasyGroups\Functions\Permission\Permission;

class UpdatePermissions {

 public static $data = [];
 private $attachment = [];

 public static function execute($player) : void {
  $plugin = Loader::getInstance();
  $config = new Config($plugin->getDataFolder() . "groups.yml");
  $player->addAttachment($plugin)->clearPermissions();

  foreach ($config->getAll() as $group => $data) {
   foreach ($config->getAll()[$group]["permission"] as $perm) {
    $player->addAttachment($plugin)->setPermission($perm, false);
   }
  }

  foreach ($config->getAll()[_Group::get($player)]["permission"] as $perm) {
   $player->addAttachment($plugin)->setPermission($perm, true);
  }

 }

}
