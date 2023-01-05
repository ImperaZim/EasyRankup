<?php

namespace ImperaZim\EasyGroups\forms\presets;

use pocketmine\Server;
use pocketmine\player\Player;
use ImperaZim\EasyGroups\EasyGroups; 

class addPermissionForm extends \ImperaZim\EasyGroups\forms\FormAPI {
 
 const NO_PERMISSION = 0; 
 public static $group = []; 
 
 public static function selectGroup(Player $player) {
  $plugin = EasyGroups::getInstance();
  $p1 = !$player->hasPermission("easygroups.command") ? 0 : 1;
  $p2 = !$player->hasPermission("easygroups.command.permission") ? 0 : 1;
  $p3 = $p1 + $p2;
  if ($p3 == self::NO_PERMISSION) {
   return $player->sendMessage($plugin->getUtils()->replace([], [], $plugin->getMessages()->getNested("messages.error.WITHOUT_PERMISSION")));
  }
  $form = self::sendCustomForm(function($player, $data = null){
   $plugin = EasyGroups::getInstance();
   if ($data == null) return; 
   $groups = "";
   foreach ($plugin->getGroupManager()->getAll() as $group => $dat) {
    if ($groups != "") $groups .= "^{$group}";
    if ($groups == "") $groups .= "{$group}";
   }
   self::$group["selected"] = explode("^", $groups)[$data["group"]];
   self::sendDetail($player);
  });
  $plugin = EasyGroups::getInstance();
  $groups = "";
  foreach ($plugin->getGroupManager()->getAll() as $group => $data) {
   $permissions = count($plugin->getGroupManager()->getPermissionManager()->getPermissions($group));
   if ($groups != "") $groups .= "^§7[{$permissions}] {$group} => {$data['tag']}";
   if ($groups == "") $groups .= "§7[{$permissions}] {$group} => {$data['tag']}";
  }
  $form->setTitle("§eEasyGroups §7» Add Permission");
  $form->addDropdown("§7Select Group", explode("^", $groups), 0, "group"); 
  $form->sendToPlayer($player);
  return $form;  
 }
 
 public static function sendDetail(Player $player) {
  $form = self::sendCustomForm(function($player, $data = null){
   $plugin = EasyGroups::getInstance();
   if ($data == null) return;
   self::$group["permission"] = $data["permission"] ?? "";
   self::sendConfirmation($player);
  });
  $form->setTitle("§7Add Permission » " . self::$group["selected"]);
  $form->addInput("§7Permission | Example: §eeasygroups.command", " §7permission name",  "plugin.permission.name", "permission"); 
  $form->sendToPlayer($player);
  return $form;    
 }
 
 public static function sendConfirmation(Player $player) {
  $plugin = EasyGroups::getInstance();
  $form = self::sendQuestionForm(function($player, $data = null){
  if ($data == null || $data == false) self::sendDetails($player);
   $plugin = EasyGroups::getInstance();
   $group = self::$group["selected"];
   $groups = $plugin->getGroupManager();
   $permission = self::$group["permission"];
   $permissions = $groups->getPermissionManager(); 
   
   if ($permissions->setPermission($group, $permission)) {
    $player->sendMessage($plugin->getUtils()->replace(["{group}", "{permission}"], [$group, $permission], $plugin->getMessages()->getNested("messages.permission.ADD.SUCCESSFULLY_ADD")));
   }else{
    $player->sendMessage($plugin->getUtils()->replace([], [], "{prefix} §7permission error!!"));
   }
  });
  $alert = "";
  $group = self::$group["selected"];
  $groups = $plugin->getGroupManager();
  $permission = self::$group["permission"];
  $permissions = $groups->getPermissionManager(); 
  $form->setTitle("§7Add Permission » confirmation");
  if ($permission == "") $alert .= "§7[§c!§7] §cThe permission is empty! \n";
  if (in_array($permission, $permissions->getPermissions($group))) $alert .= "§7[§c!§7] §cThe permission {$permission} already exists in the permissions list of the group {$group} §cif you add it the group will have a duplicate permission! \n";
  
  $form->setContent($alert.$plugin->getUtils()->replace(["{group}", "{permission}"], [$group, $permission], $plugin->getMessages()->getNested("messages.permission.ADD.CONFIRMATION_FORM")));
  $form->setButton1("§aCONFIRM");
  $form->setButton2("§cDECLINE");
  $form->sendToPlayer($player);
  return $form;   
 }
 
} 
