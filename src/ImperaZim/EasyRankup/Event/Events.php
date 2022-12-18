<?php

namespace ImperaZim\EasyRankup\Event;

use ImperaZim\EasyRankup\EasyRankup;
use ImperaZim\EasyRankup\Event\PlayerEvent\JoinEvent;
use ImperaZim\EasyRankup\Event\PlayerEvent\QuitEvent;

class Events extends EasyRankup {
 
 public static function registerAll() : void {
   $events = [
    JoinEvent::class, 
    QuitEvent::class
   ];
   foreach ($events as $event) {
    self::$instance->getServer()->getPluginManager()->registerEvents(new $event(), self::$instance);
   }
  } 
 
} 
