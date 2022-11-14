<?php

namespace ImperaZim\EasyGroups\Functions\Permission;

use pocketmine\Server;
use pocketmine\utils\Config;
use ImperaZim\EasyGroups\Loader;
use ImperaZim\EasyGroups\Utils\form\FormAPI; 
use ImperaZim\EasyGroups\Functions\Groups\_Group;  

class removePerm {
 
 public static $saved = [];
 
 public static function execute($player) {
  $plugin = Loader::getInstance();
  $form = FormAPI::createCustomForm(function($player, $data = null){
   $plugin = Loader::getInstance();
   if (is_null($data)) return true;
   self::permission($player, $data["group"]);
  });
  $array = "";
  $groups = new Config($plugin->getDataFolder() . "groups.yml");
  foreach ($groups->getAll() as $group => $data){
   if($array == "") {
    $array .= $groups->getAll()[$group]["tag"] . "§r§7 (" . (count($groups->getAll()[$group]["permission"])) . " §7Permissions)";
   }else{
    $array .= "|" . $groups->getAll()[$group]["tag"] . "§r§7 (" . (count($groups->getAll()[$group]["permission"])) . " §7Permissions)";
   }
  }
  $groups = explode("|", $array);
  $form->setTitle("§cEasyGroups §7 » Select group to remove");
  $form->addLabel("§b");
  $form->addDropdown("§7Listed Groups (select max \"1\")", $groups, 0, "group");
  $form->sendToPlayer($player);
  return $form;  
 }
 
 public static function permission($player, $groupId) {
  $plugin = Loader::getInstance();
  $group = _Group::getGroupInString($groupId); 
  self::$saved["group"] = $group;
  $form = FormAPI::createCustomForm(function($player, $data = null){
   $plugin = Loader::getInstance();
   $group = self::$saved["group"];
   $messagem = $plugin->getConfig(); 
   if (is_null($data)) return true;
   if (isset($data["perm"])) return true;
   self::removePermission($player, $group, $data["perm"]);
  });
  $form->setTitle("§cEasyGroups §7 » Select permission to remove");
  $form->addLabel("§b");
  $data = new Config($plugin->getDataFolder() . "groups.yml");
  $config = $data->getAll();
  if (count($config["permission"]) >= 1) {
  $form->addDropdown("§7Listed Permissions (select max \"1\")", $config[$group]["permission"], 0, "perm");
  }else{
   $form->addLabel("§7This group no have permissions!");
  }
  $form->sendToPlayer($player);
  return $form;  
 }
 
 public static function removePermission($player, $group, $permId) {
  $plugin = Loader::getInstance(); 
  $data = new Config($plugin->getDataFolder() . "groups.yml");
  $config = $data->getAll(); 
  $tag = $config[$group]["tag"];
  $messages = $plugin->getConfig();
  $player->sendMessage(Loader::getProcessedTags(["{prefix}", "{group}", "{permission}"], [$messages->get("default.prefix"), $tag, $config[$group]["permission"][$permId]], $messages->getNested('commands.subcommands.removepermission.sucess', false)));  
  unset($config[$group]["permission"][$permId]); 
  $data->setAll($config);
  $data->save(); 
  self::reloadPermissions();
 }
 
 public static function reloadPermissions() : void {
  foreach (Server::getInstance()->getOnlinePlayers() as $player) {
   UpdatePermissions::execute($player);
  }
 }
 
} 
