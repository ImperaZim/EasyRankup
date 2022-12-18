<?php

namespace ImperaZim\EasyRankup\Event\PlayerEvent;

use pocketmine\event\Listener;
use ImperaZim\EasyRankup\EasyRankup;
use pocketmine\event\player\PlayerJoinEvent;

class JoinEvent implements Listener {

 public function Event(PlayerJoinEvent $event) : void {
  EasyRankup::$instance->getProvinder()->createProfile($event->getPlayer());
  EasyRankup::$instance->getRankManager()->getPermissionManager()->UpdatePermissions($event->getPlayer()); 
 }

}

 
