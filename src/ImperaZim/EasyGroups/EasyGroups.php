<?php

namespace ImperaZim\EasyGroups;

use pocketmine\utils\Config;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use ImperaZim\EasyGroups\groups\group;
use ImperaZim\EasyGroups\user\permission;
use ImperaZim\EasyGroups\tasks\AsyncTask;
use ImperaZim\EasyGroups\registers\command;
use ImperaZim\EasyGroups\registers\listener;
use ImperaZim\EasyGroups\provinders\DataBase;

class EasyGroups extends PluginBase {
 
 public static EasyGroups $instance;

 public static function getInstance() : EasyGroups {
  return self::$instance;
 }

 public function onLoad() : void {
  self::$instance = $this;
 } 
 
 public function onEnable() : void {
  if (DataBase::checkType()) {
   command::registerAll();
   listener::registerAll();
   $this->getGroupManager()->generatePath();
   AsyncTask::register($this, new AsyncTask());
  }
 }  
	
 public function getProvinder() {
  return DataBase::open();
 }
 
 public function getMessages() : Config {
  return new Config($this->getDataFolder() . "messages.yml");
 }  
 
 public function getUtils() : PluginUtils {
  return new PluginUtils();
 }
 
 public function getUserManager() : permission {
  return new permission();
 }
 
 public function getGroupManager() : group {
  return new group();
 }
 
 /* API FUNCTIONS */
 
 public function getGroup(Player $player) : string {
  return (string) $this->getGroupManager()->getGroup($player);
 }
 
 public function getTag(String $group) : string {
  return (string) $this->getGroupManager()->getFormat($group,"tag");
 }
 
 public function isNormal(String $group) : bool {
  return $this->getGroupManager()->getFormat($group,"type") != "vip";
 }
 
} 
