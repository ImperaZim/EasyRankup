<?php

namespace ImperaZim\EasyRankup;

use pocketmine\utils\Config;
use pocketmine\plugin\PluginBase;
use ImperaZim\EasyRankup\Event\Events; 
use ImperaZim\EasyRankup\Command\Commands; 
use ImperaZim\EasyRankup\Functions\DataBase\DataBase;
use ImperaZim\EasyRankup\Functions\RankManager\RankManager;

class EasyRankup extends PluginBase {
 
 public array $tasks = [];
 public static EasyRankup $instance;

 public static function getInstance() : EasyRankup {
  return self::$instance;
 }

 public function onLoad() : void {
  self::$instance = $this; 
 }  
 
 public function onEnable() : void { 
  if(DataBase::checkType()) {
   Events::registerAll();
   Commands::registerAll();
   $this->saveResource('ranks.yml');
   $this->saveResource('messages.yml');
  }
 }
 
 public function getMessages() : Config {
  return new Config($this->getDataFolder() . "messages.yml");
 } 
 
 /* API FUNCTION */
 
 public function getProvinder() {
  return DataBase::open();
 }
 
 public function getRankManager() : RankManager {
  return new RankManager();
 }
 
}
