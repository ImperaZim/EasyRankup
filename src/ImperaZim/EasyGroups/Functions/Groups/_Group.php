<?php

namespace ImperaZim\EasyGroups\Functions\Groups;

use pocketmine\utils\Config;
use pocketmine\player\Player;
use ImperaZim\EasyGroups\Loader;
use ImperaZim\EasyGroups\Functions\Storage\SQLite3;

class _Group {

 public static function get(Player $player) {
  $data = SQLite3::table()->query("SELECT tag FROM profile WHERE name='{$player->getName()}'");
  $plugin = Loader::getInstance();
  $data = $data->fetchArray(SQLITE3_ASSOC);
  $tag = $data['tag'] ?? SQLITE3::default();
  $config = new Config($plugin->getDataFolder() . "groups.yml");
  return isset($config->getAll()[$tag]) ? $tag : SQLITE3::default();
  }

  public static function set(Player $player, String $group) : void {
   SQLite3::table()->query("UPDATE profile SET tag='".$data->real_escape_string($group)."' WHERE name='".$data->real_escape_string($player->getName())."'"); 
  }

  public static function default () : void {
   $default = SQLite3::default();
    $plugin = Loader::getInstance();
    $config = new Config($plugin->getDataFolder() . "groups.yml");
    if (!isset($config->getAll()[$default])) {
     $config = new Config($plugin->getDataFolder() . "groups.yml", Config::YAML, [
      $default => [
       "tag" => "§e[{$default}]",
       "type" => "normal",
       "chat" => "§e[{$default}]§r§f {name}: §7{message}",
       "nametag" => "§e[{$default}] {name}",
       "permission" => []
      ]
     ]);
     $config->save();
    }
   }

   public static function loadGroups() : bool {
    $state = 0;
    $plugin = Loader::getInstance();
    $config = new Config($plugin->getDataFolder() . "groups.yml");
    foreach ($config->getAll() as $group) {
     if (!isset($group["tag"])) $state = $state + 1;
     if (!isset($group["type"])) $state = $state + 1;
     if (!isset($group["chat"])) $state = $state + 1;
     if (!isset($group["nametag"])) $state = $state + 1;
     if (!isset($group["permission"])) $state = $state + 1;
    }
    if ($state >= 1) {
     $plugin->disable();
     return false;
    }
    return true;
   }
   
   public static function getGroupInString(Int $groupId) : string {
    $plugin = Loader::getInstance();
    $config = new Config($plugin->getDataFolder() . "groups.yml"); 
    
    $groups = "";
    foreach ($config->getAll() as $group => $data) {
     $groups .= "$group:";
    }
    $groups = explode(":", $groups);
    if(!isset($groups[$groupId])) {
     return (string) "unknow";
    }
    return (string) $groups[$groupId];
   }   

  }
