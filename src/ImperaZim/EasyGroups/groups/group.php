<?php

namespace ImperaZim\EasyGroups\groups;

use pocketmine\utils\Config;
use pocketmine\player\Player;
use ImperaZim\EasyGroups\EasyGroups;

class group {

 public $DEFAULT_GROUPS = ["guest", "owner"];

 public function getGroup(Player $player) {
  return EasyGroups::getInstance()->getProvinder()->getValue($player, "tag");
 }

 public function setGroup(Player $player, String $group, Int $time) : bool {
  $plugin = EasyGroups::getInstance();
  if ($this->getGroup($player) == $group) return true;
  
  if (!isset($this->getAll()[$group])) return false;
  
  $color = str_replace(["[", "{$group}","]"], [""], $this->getFormat($group, "tag"));
  $p_color = $color . $player->getName(); 
  
  $plugin->getProvinder()->setValue($player, "tag", $group);
  $this->getPermissionManager()->updatePermissions($player);   
  if ($time >= 1) $plugin->getProvinder()->setValue($player, "time", $time);
  if ($plugin->getConfig()->getNested("group.alert.enable")) {
   $state = 0;
   $type = strtolower($this->getFormat($group, "type")) == "vip";
   $only = $plugin->getConfig()->getNested("group.alert.only-vip");
   if ($only) $state = $state + 1;
   if ($type) $state = $state + 1;
   if (in_array($state, [0, 2])) {
    $messages = [];
    foreach ($plugin->getConfig()->getNested("group.alert.message") as $message) {
     
     $msg = $plugin->getUtils()->replace(["{player}", "{group}"], [$p_color, $this->getFormat($group, "tag")], $message);
     array_push($messages, $msg);
    }
    foreach ($plugin->getServer()->getOnlinePlayers() as $player) {
     $player->sendTitle($messages[0], $messages[1]); 
    } 
   }
  }
  
  return true;
 }

 public function getFormat(String $group, String $format) {
  return $this->getAll()[$group][$format];
 }

 public function getDefault() : string {
  return EasyGroups::getInstance()->getConfig()->getNested("group.default");
 }

 public function generatePath() : void {
  $plugin = EasyGroups::getInstance();
  $plugin->saveResource('messages.yml');
  if (count($this->getAll()) < 1) {
   foreach ($this->DEFAULT_GROUPS as $group) {
    if (!isset($this->getAll()[$group])) {
     $this->create(null, $group, "[{$group}]", "normal", "{tag} {player}", "{tag} {player}: {message}", ["easygroups.command", "easygroups.set.owner", "easygroups.set.guest"]);
    }
   }
  }
  if (!isset($this->getAll()[$this->getDefault()])) {
   $this->create(null, $this->getDefault(), "[{$this->getDefault()}]", "normal", "{tag} {player}", "{tag} {player}: {message}", ["easygroups.command", "easygroups.set.owner", "easygroups.set.guest"]);
  }
 }

 public function timeout(Player $player) : bool {
  $group = $this->getGroup($player);
  $provinder = EasyGroups::getInstance()->getProvinder();
  if ($provinder->getValue($player, "time") <= 0) return false;
  if ($group != $this->getDefault()) {
   $time = $provinder->getValue($player, "time");
   if ($time > 0 || $time <= 1000000000) {
    if ($time == 1) {
     $this->setGroup($player, $this->getDefault(), 0);
     return true;
    }
    $out = (int) $time - 1;
    $provinder->setValue($player, "time", $out);
   }
  }
  return false;
 }

 public function isValid(Player $player) : bool {
  $group = $this->getGroup($player);
  $provinder = EasyGroups::getInstance()->getProvinder();
  if (isset($this->getAll()[$group]["tag"])) {
   return false;
  }
  $this->setGroup($player, $this->getDefault(), 0);
  return true;
 }

 public function create($creator, String $group, String $tag, String $type, String $nametag, String $prefix, array $permissions) : bool {
  $plugin = EasyGroups::getInstance();

  if (isset($this->getAll()[$group])) {
   if ($creator != null) $creator->sendMessage($plugin->getUtils()->replace(["{group}"], [$this->getAll()[$group]["tag"]], $plugin->getMessages()->getNested("messages.create.GROUP_ALREADY_EXISTS")));
   return false;
  }

  $config = new Config($plugin->getDataFolder() . "groups.yml", Config::YAML, [$group => [
   "tag" => $tag,
   "type" => $type,
   "nametag" => $nametag,
   "chat" => $prefix,
   "permissions" => $permissions
  ]]);
  $config->save();
  return true;
 }

 public function delete($creator, String $group) : bool {
  $plugin = EasyGroups::getInstance();

  if (!isset($this->getAll()[$group])) {
   if ($creator != null) $creator->sendMessage($plugin->getUtils()->replace(["{group}"], [$this->getAll()[$group]["tag"]], $plugin->getMessages()->getNested("messages.delete.GROUP_DOES_NOT_EXISTS")));
   return false;
  }

  $data = new Config($plugin->getDataFolder() . "groups.yml");
  $config = $this->getAll();

  unset($config[$group]);
  $data->setAll($config); $data->save();
  return true;
 }

 public function getPermissionManager() : permission {
  return new permission();
 }

 public function getConfig() : Config {
  return new Config(EasyGroups::getInstance()->getDataFolder() . "groups.yml");
 }

 public function getAll() : array {
  return (new Config(EasyGroups::getInstance()->getDataFolder() . "groups.yml"))->getAll();
 }

}
 
