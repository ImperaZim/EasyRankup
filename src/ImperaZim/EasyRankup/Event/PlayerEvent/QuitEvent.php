<?php

namespace ImperaZim\EasyRankup\Event\PlayerEvent;

use pocketmine\event\Listener;
use ImperaZim\EasyRankup\EasyRankup;
use pocketmine\event\player\PlayerQuitEvent;

class QuitEvent implements Listener {

 public function Event(PlayerQuitEvent $event) : void {
  $player = $event->getPlayer();
  $plugin = EasyRankup::getInstance();
  if (isset($plugin->tasks[$player->getName()])) {
   unset($plugin->tasks[$player->getName()]);
  }
 }

}

  
