<?php

namespace ImperaZim\EasyGroups\forms\presets;

use pocketmine\player\Player;
use ImperaZim\EasyGroups\EasyGroups; 

class DeleteGroupForm extends \ImperaZim\EasyGroups\forms\FormAPI {
 
 const NO_PERMISSION = 0;
 public static $group = []; 
 
 public static function selectGroup(Player $player) {
  $plugin = EasyGroups::getInstance();
  $p1 = !$player->hasPermission("easygroups.command") ? 0 : 1;
  $p2 = !$player->hasPermission("easygroups.command.delete") ? 0 : 1;
  $p3 = $p1 + $p2;
  if ($p3 == self::NO_PERMISSION) {
   return $player->sendMessage($plugin->getUtils()->replace([], [], $plugin->getMessages()->getNested("messages.error.WITHOUT_PERMISSION")));
  }
  $form = self::sendCustomForm(function($player, $data = null){
   $plugin = EasyGroups::getInstance();
   if ($data == null) return; 
   $groups = "";
   foreach ($plugin->getGroupManager()->getAll() as $group => $dat) {
    if ($group != $plugin->getGroupManager()->getDefault()) { 
     if ($groups != "") $groups .= "^$group"; 
     if ($groups == "") $groups .= "$group";
    }
   }
   self::$group["selected"] = explode("^", $groups)[$data["group"]];
   self::sendConfirmation($player);
  });
  $form->setTitle("§eEasyGroups§7 » Delete Group");
  $groups = "";
  foreach ($plugin->getGroupManager()->getAll() as $group => $dat) {
   if ($group != $plugin->getGroupManager()->getDefault()) {
    if ($groups != "") $groups .= "^§7{$group} => {$dat['tag']}";
    if ($groups == "") $groups .= "§7{$group} => {$dat['tag']}";
   }
  }
  $form->addLabel("§7[§c!§7] Default group does not appear!", "log");
  $form->addDropdown("§7Select Group", explode("^", $groups), 0, "group");
  $form->sendToPlayer($player);
  return $form; 
 }
 
 public static function sendConfirmation(Player $player) {
  $plugin = EasyGroups::getInstance();
  $form = self::sendQuestionForm(function($player, $data = null) {
   $plugin = EasyGroups::getInstance();
   if ($data == null || $data == false) self::selectGroup($player);
   $tag = $plugin->getGroupManager()->getAll()[self::$group["selected"]]["tag"];
   if ($plugin->getGroupManager()->delete($player, self::$group["selected"])) {
    $player->sendMessage($plugin->getUtils()->replace(["{group}"], [$tag], $plugin->getMessages()->getNested("messages.delete.SUCCESSFULLY_DELETE"))); 
   }
  });
  $tag = $plugin->getGroupManager()->getAll()[self::$group["selected"]]["tag"]; 
  $form->setTitle("Delete Group » " . $tag);
  $form->setContent($plugin->getUtils()->replace(["{group}"], [$tag], $plugin->getMessages()->getNested("messages.delete.CONFIRMATION_FORM"))); 
  $form->setButton1("§aCONFIRM");
  $form->setButton2("§cDECLINE");
  $form->sendToPlayer($player);
  return $form;  
 }
 
}