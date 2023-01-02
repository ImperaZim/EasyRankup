<?php

namespace ImperaZim\EasyGroups;

use pocketmine\utils\Config;
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
  //var_dump($this->getProvinder()->getAllPermissions());
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
 
} 
