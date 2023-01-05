<?php

namespace ImperaZim\EasyGroups\forms\presets;

use pocketmine\Server;
use pocketmine\player\Player;
use ImperaZim\EasyGroups\EasyGroups; 

class RemovePermissionForm extends \ImperaZim\EasyGroups\forms\FormAPI {
 
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
    $permissions = count($plugin->getGroupManager()->getPermissionManager()->getPermissions($group));
    if ($permissions >= 1) {
     if ($groups != "") $groups .= "^{$group}";
     if ($groups == "") $groups .= "{$group}";
    }
   }
   self::$group["selected"] = explode("^", $groups)[$data["group"]];
   self::selectPermission($player);
  });
  $plugin = EasyGroups::getInstance();
  $groups = "";
  foreach ($plugin->getGroupManager()->getAll() as $group => $data) {
   $permissions = count($plugin->getGroupManager()->getPermissionManager()->getPermissions($group));
   if ($permissions >= 1) {
    if ($groups != "") $groups .= "^§7[{$permissions}] {$group} => {$data['tag']}";
    if ($groups == "") $groups .= "§7[{$permissions}] {$group} => {$data['tag']}";
   }
  }
  $form->setTitle("§eEasyGroups §7 » Remove Permission");
  $form->addDropdown("§7Select Group", explode("^", $groups), 0, "group"); 
  $form->sendToPlayer($player);
  return $form;  
 }
 
 public static function selectPermission(Player $player) {
  $plugin = EasyGroups::getInstance();
  $form = self::sendCustomForm(function($player, $data = null){
   $plugin = EasyGroups::getInstance();
   if ($data == null) return;
   $permissions = "";
  
   foreach ($plugin->getGroupManager()->getPermissionManager()->getPermissions(self::$group["selected"]) as $permission) {
    if ($permissions != "") $permissions .= "^§e» §7{$permission}";
    if ($permissions == "") $permissions .= "§e» §7{$permission}";
   } 
   
   
   self::$group["permission"] = explode("^", $permissions)[$data["permission"]];
   self::sendConfirmation($player);
  });
  
  $permissions = "";
  
  foreach ($plugin->getGroupManager()->getPermissionManager()->getPermissions(self::$group["selected"]) as $permission) {
   if ($permissions != "") $permissions .= "^§e» §7{$permission}";
   if ($permissions == "") $permissions .= "§e» §7{$permission}";
  }
  
  $form->setTitle("§7Remove Permission » " . self::$group["selected"]);
  $form->addDropdown("§7Select Permission", explode("^", $permissions), 0, "permission");
  $form->sendToPlayer($player);
  return $form;     
 }
 
 public static function sendConfirmation(Player $player) {
  $form = self::sendQuestionForm(function($player, $data = null){
  if ($data == null || $data == false) self::sendDetails($player);
   $plugin = EasyGroups::getInstance();
   if ($data == null || $data == false) self::selectGroup($player);
   $group = self::$group["selected"];
   $groups = $plugin->getGroupManager();
   $permission = self::$group["permission"];
   $permissions = $groups->getPermissionManager(); 
   
   if ($permissions->unsetPermission($group, $permission)) {
    $player->sendMessage($plugin->getUtils()->replace(["{group}", "{permission}"], [$group, $permission], $plugin->getMessages()->getNested("messages.permission.REMOVE.SUCCESSFULLY_REMOVE")));
   }else{
    $player->sendMessage($plugin->getUtils()->replace([], [], "{prefix} §7permission error!!"));
   }
  }); 
  $group = self::$group["selected"];
  $plugin = EasyGroups::getInstance();
  $permission = self::$group["permission"]; 
   
  $form->setTitle("§7Remove Permission » confirmation"); 
  $form->setContent($plugin->getUtils()->replace(["{group}", "{permission}"], [$group, $permission], $plugin->getMessages()->getNested("messages.permission.REMOVE.CONFIRMATION_FORM")));
  $form->setButton1("§aCONFIRM");
  $form->setButton2("§cDECLINE");
  $form->sendToPlayer($player);
  return $form;    
 }
 
}