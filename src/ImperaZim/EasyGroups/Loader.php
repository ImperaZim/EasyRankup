<?php

namespace ImperaZim\EasyGroups;

use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\plugin\PluginBase;
use ImperaZim\EasyGroups\Task\AsyncTask;
use ImperaZim\EasyGroups\Events\ChatGroupEvent;
use ImperaZim\EasyGroups\Functions\Groups\_Group;
use ImperaZim\EasyGroups\Events\DeafultGroupEvent;
use ImperaZim\EasyGroups\Functions\Storage\SQLite3;
use ImperaZim\EasyGroups\Commands\EasyGroupsCommand;

class Loader extends PluginBase {

 public static $instance = null;

 public static function getInstance() : Loader {
  return self::$instance;
 }

 public function onLoad() : void {
  self::$instance = $this;
 }

 public function onEnable() : void {
  _Group::default();
  SQLite3::createTable();
  self::registerEvents();
  self::registerCommandMap();
  self::registerRepeatingTask();
  _Group::loadGroups();
 }

  public static function registerEvents() : void {
   $events = [ChatGroupEvent::class,
    DeafultGroupEvent::class];
   foreach ($events as $event) {
    Server::getInstance()->getPluginManager()->registerEvents(new $event(), self::$instance);
   }
  }

  public static function registerCommandMap() : void {
   $commands = [
    "EasyGroups" => new EasyGroupsCommand()
   ];
   foreach ($commands as $command) {
    Server::getInstance()->getCommandMap()->register("EasyGroups", $command);
   }
  }

  public static function registerRepeatingTask() : void {
   self::$instance->getScheduler()->scheduleRepeatingTask(new AsyncTask(self::$instance), 5);
  }
  
  public static function getProcessedTags(array $tag, array $processed, String $message) : String {
   return str_replace($tag, $processed, $message);
  }
  
  public function ImportTagByPlayer($player) {
   $group = _Group::get($player);
   $config = new Config($this->getDataFolder() . "groups.yml");  
   return $config->getAll()[$group]["tag"] . "§r"; 
  }
  
  public function ImportPrefixByPlayer($player) {
   return _Group::get($player);
  }
  
  public function ImportTypeByPrefix($prefix) {
   $config = new Config($this->getDataFolder() . "groups.yml");  
   return $config->getAll()[$prefix]["type"] . "§r";  
  }
  
  public function disable() : void {
   $this->getLogger()->warning("A estrutura do arquivo \"groups.yml\" não está funcionando perfeitamente.");
   $this->getServer()->getPluginManager()->disablePlugin($this);
  }

 }
