<?php

namespace ImperaZim\EasyGroups\registers;

use ImperaZim\EasyGroups\EasyGroups;
use ImperaZim\EasyGroups\listeners\PlayerEvent;

class listener extends EasyGroups {
 
 public static function registerAll() : void {
   $events = [PlayerEvent::class];
   foreach ($events as $event) {
    self::getInstance() ->getServer()->getPluginManager()->registerEvents(new $event(), self::$instance);
   }
  } 
 
} 
  
