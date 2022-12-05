<?php

namespace ImperaZim\EasyGroups\Task;

use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\scheduler\Task;
use ImperaZim\EasyGroups\Loader;
use pocketmine\scheduler\CancelTaskException; 
use ImperaZim\EasyGroups\Functions\Groups\_Group; 
use ImperaZim\EasyGroups\Functions\Storage\SQLite3;
use ImperaZim\EasyGroups\Functions\Groups\UpdateGroup; 

class AsyncTask extends Task {

 public $state = 0;
 
 public static function register($loader, AsyncTask $task) : void {
  $loader->getScheduler()->scheduleRepeatingTask($task, 10);
 }
  
 public function unregister() : void {
  $this->state = 1;
 }

 public function onRun() : void {
  if ($this->state != 0) {
   throw new CancelTaskException();
  } 
  $plugin = Loader::getInstance();
  $server = Server::getInstance();
  $config = new Config($plugin->getDataFolder() . "groups.yml");
  
  foreach ($server->getOnlinePlayers() as $player) { 
   $name = $player->getName();
   $group = _Group::get($player);
   _Group::set($player, $group);
   if(isset($config->getAll()[$group])){
    $tag = $config->getAll()[$group]["tag"] ?? "[]";
    $nametag = $config->getAll()[$group]["nametag"] ?? "{tag} {name}";
    $nametag = Loader::getProcessedTags(["{tag}", "{name}"], [$tag, $name], $nametag);
    $player->setNameTag($nametag);
   }
  } 
  
 }
 
}
