<?php

namespace ImperaZim\EasyGroups\Functions\Permission;

use pocketmine\Server;
use pocketmine\utils\Config;
use ImperaZim\EasyGroups\Loader;
use ImperaZim\EasyGroups\Utils\form\FormAPI; 
use ImperaZim\EasyGroups\Functions\Groups\_Group;  
use ImperaZim\EasyGroups\Functions\Permission\UpdatePermissions;  

class addPerm {
 
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
    $array .= $groups->getAll()[$group]["tag"] . "§r§7 (" . (count($groups->getAll()[$group]["permission"])) . "§7 ermissions)";
   }else{
    $array .= "|" . $groups->getAll()[$group]["tag"] . "§r§7 (" . (count($groups->getAll()[$group]["permission"])) . "§7 Permissions)";
   }
  }
  $groups = explode("|", $array);
  $form->setTitle("§cEasyGroups §7 » Select group to add");
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
   $permission = $data["permission"];
   if (strlen($permission) <= 0 || $permission == null) {
    $player->sendMessage(Loader::getProcessedTags(["{prefix}"], [$messagem->get("default.prefix")], $messagem->getNested('commands.subcommands.addpermission.failed', false))); 
    return true;
   }
   self::addPermission($player, $group, $permission);
  });
  $groups = new Config($plugin->getDataFolder() . "groups.yml");
  $tag = $groups->getAll()[$group]["tag"]; 
  $form->setTitle("§cEasyGroups §7 » {$tag}");
  $form->addLabel("§b");
  $form->addInput("§7Write permission to add to §e{$tag}", "permission.name", "", "permission");
  $form->sendToPlayer($player);
  return $form;   
 }
 
 public static function addPermission($player, $group, $permission) {
  $plugin = Loader::getInstance(); 
  $data = new Config($plugin->getDataFolder() . "groups.yml");
  $config = $data->getAll();
  $messages = $plugin->getConfig();
  /* WHAT???? 
  if (count($data->getAll()[$group]["permission"]) >= 1){
   if (in_array($permission, $data->getAll()[$group]["permission"])) {
    $tag = $config[$group]["tag"]; 
    $player->sendMessage(Loader::getProcessedTags(["{prefix}", "{group}", "{permission}"], [$messagem->get("default.prefix"), $tag, $permission], $messagem->getNested('commands.subcommands.addpermission.has_perm', false)));   
    return true;
   }
  }
  */
  array_push($config[$group]["permission"], $permission); 
  $data->setAll($config);
  $data->save(); 
  $tag = $config[$group]["tag"];
  $player->sendMessage(Loader::getProcessedTags(["{prefix}", "{group}", "{permission}"], [$messages->get("default.prefix"), $tag, $permission], $messages->getNested('commands.subcommands.addpermission.sucess', false)));  
  self::reloadPermissions();
 }
 
 public static function reloadPermissions() : void {
  foreach (Server::getInstance()->getOnlinePlayers() as $player) {
   UpdatePermissions::execute($player);
  }
 }
 
} 
