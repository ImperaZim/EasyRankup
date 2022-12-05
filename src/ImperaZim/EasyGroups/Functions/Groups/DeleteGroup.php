<?php

namespace ImperaZim\EasyGroups\Functions\Groups;

use pocketmine\Server;
use pocketmine\utils\Config;
use ImperaZim\EasyGroups\Loader;
use ImperaZim\EasyGroups\Utils\form\GroupForm;
use ImperaZim\EasyGroups\Functions\Groups\_Group; 
use ImperaZim\EasyGroups\Functions\Storage\SQLite3; 

class DeleteGroup {

 public static $saved = [];

 public static function execute($player) {
  $plugin = self::getPlugin();
  $form = GroupForm::createCustomForm(function($player, $data = null){
   $plugin = self::getPlugin();
   if (is_null($data)) return true;
   self::confirm($player, $data["group"]);
  });
  
  $array = "";
  $groups = new Config($plugin->getDataFolder() . "groups.yml");
  foreach ($groups->getAll() as $group => $data){
   if($array == "") {
    $array .= "§7{$group} => " . $groups->getAll()[$group]["tag"];
   }else{
    $array .= "|§7{$group} => " . $groups->getAll() [$group]["tag"];
   }
  }
  $groups = explode("|", $array);
  $form->setTitle("§cEasyGroups §7 » Delete Group");
  $form->addLabel("§b");
  $form->addDropdown("§7Listed Groups (select max \"1\")", $groups, 0, "group");
  $form->sendToPlayer($player);
  return $form;  
 }
 
 public static function confirm($player, $group) {
  $plugin = self::getPlugin();
  $messagem = $plugin->getConfig();
  self::$saved["group"] = $group; 
  $group = _Group::getGroupInString($group); 
  $form = GroupForm::createModalForm(function($player, $data = null){
   $plugin = self::getPlugin();
   if (is_null($data)) return true; 
   if ($data == true) {
    self::delete($player); 
   }
  });
  $form->setTitle("§cEasyGroups §7 » Delete Group \"{$group}\"");
  $form->setContent(Loader::getProcessedTags(["{prefix}", "{group}"], [$messagem->get("default.prefix"), $group], $messagem->getNested('commands.subcommands.deletecommand.form_confirm', false)));
  $form->setButton1("§l§a[CONFIRM]");
  $form->setButton2("§l§c[DECLINE]");
  $form->sendToPlayer($player);
  return $form;   
 }
 
 public static function delete($player) {
  $def = SQLite3::default();
  $group = self::$saved["group"];
  $plugin = Loader::getInstance();
  $messagem = $plugin->getConfig();  
  $group = _Group::getGroupInString(self::$saved["group"]);
  $data = new Config($plugin->getDataFolder() . "groups.yml");
  $config = $data->getAll();
  $default_tag = $config[$def]['tag'];
  $deleted_tag = $config[$group]['tag']; 
  if ($group == SQLite3::default()) {
   $player->sendMessage(Loader::getProcessedTags(["{prefix}", "{group}"], [$messagem->get("default.prefix"), $group], $messagem->getNested('commands.subcommands.deletecommand.has_default', false)));  
   return true;
  }
  if(!isset($config[SQLite3::default()])){
   _Group::default();
  }
  if (isset($config[$group])) {
   foreach (Server::getInstance()->getOnlinePlayers() as $players) {
    if(_Group::get($player) == $group) {
     _Group::set($player, $def);
     $players->sendMessage(Loader::getProcessedTags(["{prefix}", "{deleted_group}", "{default_group}"], [$messagem->get("default.prefix"), $deleted_tag, $default_tag], $messagem->getNested('commands.subcommands.deletecommand.player_notify', false))); 
    }
   }
   unset($config[$group]);
   $data->setAll($config);
   $data->save();
   SQLite3::table()->query("UPDATE profile SET tag='" . $def . "' WHERE tag='" . $group . "';");
   $player->sendMessage(Loader::getProcessedTags(["{prefix}", "{deleted_group}", "{default_group}"], [$messagem->get("default.prefix"), $deleted_tag, $default_tag], $messagem->getNested('commands.subcommands.deletecommand.sucess', false))); 
  }else{
   $player->sendMessage(Loader::getProcessedTags(["{prefix}", "{group}"], [$messagem->get("default.prefix"), $deleted_tag], $messagem->getNested('commands.subcommands.deletecommand.group_no_exist', false))); 
  }
 }
 
 public static function getPlugin() : Loader {
  return Loader::getInstance();
 }

} 
