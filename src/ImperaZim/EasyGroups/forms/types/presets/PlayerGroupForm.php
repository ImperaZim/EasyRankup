<?php

namespace ImperaZim\EasyGroups\forms\presets;

use pocketmine\Server;
use pocketmine\player\Player;
use ImperaZim\EasyGroups\EasyGroups; 

class PlayerGroupForm extends \ImperaZim\EasyGroups\forms\FormAPI {
 
 public static $group = []; 
 const NO_PERMISSION = 0;
 
 public static function sendDetails(Player $player) {
  $groups = [];
  $plugin = EasyGroups::getInstance();
  foreach ($plugin->getGroupManager()->getAll() as $group => $str) {
   if ($player->hasPermission("easygroups.set.{$group}")) {
    array_push($groups, $group);
   }
  }
  if (count($groups) == self::NO_PERMISSION) {
   return $player->sendMessage($plugin->getUtils()->replace([], [], $plugin->getMessages()->getNested("messages.error.WITHOUT_PERMISSION")));
  }
  $form = self::sendCustomForm(function($player, $data = null){
   $plugin = EasyGroups::getInstance();
   if ($data == null) return;
   $groups = [];
   foreach ($plugin->getGroupManager()->getAll() as $group => $str) {
    $p1 = !$player->hasPermission("easygroups.command") ? 0 : 1;
    $p2 = !$player->hasPermission("easygroups.set.{$group}") ? 0 : 1;
    $p3 = (int) $p1 + $p2;
    if ($p3 != self::NO_PERMISSION) {
     array_push($groups, $group);
    }
   }
   
   $target = Server::getInstance()->getPlayerExact($data["player"]);
   if (!$target instanceof Player) return $player->sendMessage($plugin->getUtils()->replace(["{player}"], [$data["player"]], $plugin->getMessages()->getNested("messages.update.OFFLINE_PLAYER")));
   $time = in_array($data["time"], ["", "0"]) ? 1000000001 : $data["time"];
   $time = is_numeric($time) ? $time : 1000000001;
   self::$group["name"] = $groups[$data["group"]];
   self::$group["time"] = (int) $time;
   self::$group["player"] = $data["player"];
   self::sendConfirmation($player);
  });
  
  $plugin = EasyGroups::getInstance();
  $groups = [];
  foreach ($plugin->getGroupManager()->getAll() as $group => $str) {
   $p1 = !$player->hasPermission("easygroups.command") ? 0 : 1;
   $p2 = !$player->hasPermission("easygroups.set.{$group}") ? 0 : 1;
   $p3 = (int) $p1 + $p2;
   if ($p3 != self::NO_PERMISSION) {
    array_push($groups, "§7{$group} => {$str['tag']}");
   }
  }
  
  $form->setTitle("§eEasyGroups §7 » Update Group"); 
  $form->addInput("§7Player Nickname | Example: §e{$player->getName()}", " §7player nickname",  "{$player->getName()}", "player");
  $form->addDropdown("§7Select Group", $groups, 0, "group");
  $form->addInput("§7Time (in seconds) | Example: §e3600 §7(1h)", " §7time",  "0", "time");
  $form->addLabel("§7[§c!§7] Keep 0 or empty to not set a limit!", "log");
  $form->sendToPlayer($player);
  return $form;  
 }
 
 public static function sendConfirmation(player $player) {
  $plugin = EasyGroups::getInstance();
  $form = self::sendQuestionForm(function($player, $data = null){
  if ($data == null || $data == false) self::sendDetails($player);
   $plugin = EasyGroups::getInstance();
   $target = Server::getInstance()->getPlayerExact(self::$group["player"]);
   $time = self::$group["time"];
   $group = str_replace(["[", "]"], [""], self::$group["name"]);
   if ($plugin->getGroupManager()->setGroup($target, $group, $time)) {
    $player->sendMessage($plugin->getUtils()->replace(["{player}", "{group}"], [self::$group["player"], $plugin->getGroupManager()->getAll()[$group]["tag"]], $plugin->getMessages()->getNested("messages.update.SUCCESSFULLY_UPDATE.TO_SENDER")));
    $target->sendMessage($plugin->getUtils()->replace(["{group}"], [$plugin->getGroupManager()->getAll()[$group]["tag"]], $plugin->getMessages()->getNested("messages.update.SUCCESSFULLY_UPDATE.TO_TARGET")));
    return;
   }
   $player->sendMessage($plugin->getUtils()->replace(["{group}"], [$plugin->getGroupManager()->getAll()[$group]["tag"]], $plugin->getMessages()->getNested("messages.update.GROUP_DOES_NOT_EXISTS ")));
  });
  $target = Server::getInstance()->getPlayerExact(self::$group["player"]);
  $form->setTitle("§7Update Group » " . $target->getName());
  $form->setContent($plugin->getUtils()->replace(["{player}", "{old_group}", "{new_group}"], [self::$group["player"], $plugin->getGroupManager()->getAll()[$plugin->getGroupManager()->getGroup($target)]["tag"], $plugin->getGroupManager()->getAll()[self::$group["name"]]["tag"]], $plugin->getMessages()->getNested("messages.update.CONFIRMATION_FORM")));
  $form->setButton1("§aCONFIRM");
  $form->setButton2("§cDECLINE");
  $form->sendToPlayer($player);
  return $form;  
 }
 
}