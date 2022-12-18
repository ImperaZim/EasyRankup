<?php

namespace ImperaZim\EasyRankup\Functions\RankManager;

use ImperaZim\EasyRankup\EasyRankup;

class RankPermission extends RankManager {
 
 public function __construct() { }

 public function UpdatePermissions($player) : void {
  $plugin = Easyrankup::getInstance(); 

  foreach ($this->getAll() as $rank => $data) {
   foreach ($data["permissions"] as $perm) {
    $player->addAttachment($plugin)->setPermission($perm, false);
   }
  }

  foreach ($this->getPermissionsByRank($this->getRank($player)) as $perm) {
   $player->addAttachment($plugin)->setPermission($perm, true);
  }

 }

}
 