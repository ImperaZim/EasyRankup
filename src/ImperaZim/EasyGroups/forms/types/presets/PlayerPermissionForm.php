<?php

namespace ImperaZim\EasyGroups\forms\presets;

use pocketmine\Server;
use pocketmine\player\Player;
use ImperaZim\EasyGroups\EasyGroups;

class PlayerPermissionForm extends \ImperaZim\EasyGroups\forms\FormAPI {

 const NO_PERMISSION = 0;
 public static $group = [];

 public static function sendDetail(Player $player) {
  $plugin = EasyGroups::getInstance();
  self::$group["players"] = $plugin->getServer()->getOnlinePlayers();
  $p1 = !$player->hasPermission("easygroups.command") ? 0 : 1;
  $p2 = !$player->hasPermission("easygroups.command.permission") ? 0 : 1;
  $p3 = $p1 + $p2;
  if ($p3 == self::NO_PERMISSION) {
   return $player->sendMessage($plugin->getUtils()->replace([], [], $plugin->getMessages()->getNested("messages.error.WITHOUT_PERMISSION")));
  }
  $form = self::sendCustomForm(function($player, $data = null){
   if ($data == null) return;
   $plugin = EasyGroups::getInstance();
   $players = [];
   foreach (self::$group["players"] as $playerId) {
    array_push($players, $playerId);
   }
   self::$group["player"] = $players[$data["player"]];
   self::$group["action"] = $data["type"] == 0 ? "add" : "remove";
   switch (self::$group["action"]) {
    case "add":
     return self::sendPermission($player);
    case "remove":
     return self::selectPermission($player);
   }
   unset(self::$group["players"]);
  });
  $players = [];
  foreach (self::$group["players"] as $playerId) {
   $tag = $plugin->getGroupManager()->getAll()[$plugin->getGroupManager()->getGroup($playerId)]["tag"];
   $permissions = count($plugin->getUserManager()->getPermissions($playerId)) ;
   array_push($players, "§7[§e{$permissions}§7] {$tag} §r§7{$playerId->getName()}");
  }
  $form->setTitle("§eEasyGroups §7» Player Permissions");
  $form->addDropdown("§7Select Player", $players, 0, "player"); 
  $form->addDropdown("§7Select Action", ["§7[§a+§7] add permission", "§7[§c-§7] remove permission"], 0, "type");
  $form->sendToPlayer($player); 
  return $form;
 }
 
 public static function sendPermission(player $player) {
  $form = self::sendCustomForm(function($player, $data = null){
   if ($data == null) return;
   $plugin = EasyGroups::getInstance();
   self::$group["permission"] = $data["permission"];
   self::sendConfirmation($player);
  });
  $form->setTitle("§7Add Permission » §e" . self::$group["player"]->getName());
  $form->addInput("§7Permission | Example: §eeasygroups.command", " §7permission name",  "plugin.permission.name", "permission"); 
  $form->sendToPlayer($player);
  return $form;
 }
 
 public static function selectPermission(player $player) {
  $form = self::sendCustomForm(function($player, $data = null){
   if ($data == null) return;
   $plugin = EasyGroups::getInstance();
   $permissions = $plugin->getUserManager()->getPermissions(self::$group["player"]);
   if (!isset($data["permission"])) return self::sendDetail($player);
   self::$group["permission"] = $permissions[$data["permission"]];
   self::sendConfirmation($player); 
  });
  $plugin = EasyGroups::getInstance();
  $permissions = $plugin->getUserManager()->getPermissions(self::$group["player"]);
  $permissions_str = [];
  foreach ($permissions as $permission) {
   array_push($permissions_str, "§7[§c-§7] {$permission}");
  }
  $form->setTitle("§7Remove Permission » §e" . self::$group["player"]->getName());
  if (count($permissions) <= 0) {
   $form->addLabel($plugin->getUtils()->replace([], [], $plugin->getMessages()->getNested("messages.permission.USER.REMOVE_USER_PERMISSION.UNDEFINED_PERMISSIONS")), "notice");
  }else{
  $form->addDropdown("§7Select Permission", $permissions_str, 0, "permission"); 
  }
  $form->sendToPlayer($player);
  return $form;
 }
 
 public static function sendConfirmation(Player $player) {
  $plugin = EasyGroups::getInstance();
  $form = self::sendQuestionForm(function($player, $data = null){
   if ($data == null || $data == false) self::sendDetail($player);
    $plugin = EasyGroups::getInstance();
    $target = self::$group["player"];
    $action = self::$group["action"];
    $userManager = $plugin->getUserManager();
    $permission = self::$group["permission"];
    if ($action == "add") {
     $userManager->setPermission($target, $permission);
     $player->sendMessage($plugin->getUtils()->replace(["{player}", "{permission}"], [self::$group["player"]->getName(), $permission], $plugin->getMessages()->getNested("messages.permission.USER.ADD_USER_PERMISSION.SUCCESSFULLY_ADD")));
    }else{
     $userManager->unsetPermission($target, $permission);
     $player->sendMessage($plugin->getUtils()->replace(["{player}", "{permission}"], [self::$group["player"]->getName(), $permission], $plugin->getMessages()->getNested("messages.permission.USER.REMOVE_USER_PERMISSION.SUCCESSFULLY_REMOVE")));
    }
  });
  $action = self::$group["action"];
  $permission = self::$group["permission"]; 
  if ($action == "add") {
  $form->setTitle("§7Add Permission » confirmation");
  $form->setContent($plugin->getUtils()->replace(["{player}", "{permission}"], [self::$group["player"]->getName(), $permission], $plugin->getMessages()->getNested("messages.permission.USER.ADD_USER_PERMISSION.CONFIRMATION_FORM")));
  }else{ 
  $form->setTitle("§7Remove Permission » confirmation");
  $form->setContent($plugin->getUtils()->replace(["{player}", "{permission}"], [self::$group["player"]->getName(), $permission], $plugin->getMessages()->getNested("messages.permission.USER.REMOVE_USER_PERMISSION.CONFIRMATION_FORM")));
  }
  $form->setButton1("§aCONFIRM");
  $form->setButton2("§cDECLINE");
  $form->sendToPlayer($player);
  return $form;   
 }

}