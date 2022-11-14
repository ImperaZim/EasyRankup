<?php

namespace ImperaZim\EasyGroups\Events;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use ImperaZim\EasyGroups\Functions\Storage\SQLite3;
use ImperaZim\EasyGroups\Functions\Permission\UpdatePermissions; 

class DefaultGroupEvent implements Listener {

 public function Event(PlayerJoinEvent $event) {
  SQLite3::createProfile($event->getPlayer());
  UpdatePermissions::execute($event->getPlayer());
 }

}
