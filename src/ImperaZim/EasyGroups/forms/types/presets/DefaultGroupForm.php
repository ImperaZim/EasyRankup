<?php

namespace ImperaZim\EasyGroups\forms\presets;

use pocketmine\player\Player;
use ImperaZim\EasyGroups\EasyGroups; 

class DefaultGroupForm extends \ImperaZim\EasyGroups\forms\FormAPI {
 
 const NO_PERMISSION = 0;
 public static $group = []; 
 
 public static function selectGroup(Player $player) {
  $plugin = EasyGroups::getInstance();
  $p1 = !$player->hasPermission("easygroups.command") ? 0 : 1;
  $p2 = !$player->hasPermission("easygroups.command.setdefault") ? 0 : 1;
  $p3 = $p1 + $p2;
  if ($p3 == self::NO_PERMISSION) {
   return $player->sendMessage($plugin->getUtils()->replace([], [], $plugin->getMessages()->getNested("messages.error.WITHOUT_PERMISSION")));
  }
  $form = self::sendCustomForm(function($player, $data = null){
   if ($data == null) return; 
   $groups = "";
   foreach (EasyGroups::getInstance()->getGroupManager()->getAll() as $group => $dat) {
    if ($group != EasyGroups::getInstance()->getGroupManager()->getDefault()) { 
     if ($groups != "") $groups .= "^$group"; 
     if ($groups == "") $groups .= "$group";
    }
   }
   self::$group["selected"] = explode("^", $groups)[$data["group"]];
   self::sendConfirmation($player);
  });
  $form->setTitle("§eEasyGroups §7 » Update Default Group");
  $groups = "";
  foreach (EasyGroups::getInstance()->getGroupManager()->getAll() as $group => $dat) {
   if ($group != EasyGroups::getInstance()->getGroupManager()->getDefault()) {
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
  $form = self::sendQuestionForm (function($player, $data = null) {
   if ($data == null || $data == false) self::selectGroup($player);
   $config = EasyGroups::getInstance()->getConfig();
   $config->setNested("group.default", self::$group["selected"]);
   $config->save();
   
   $tag = EasyGroups::getInstance()->getGroupManager()->getAll()[self::$group["selected"]]["tag"];
   $player->sendMessage(EasyGroups::getInstance()->getUtils()->replace(["{group}"], [$tag], EasyGroups::getInstance()->getMessages()->getNested("messages.default.SUCCESSFULLY_UPDATE"))); 
  });
  $plugin = EasyGroups::getInstance();
  $oldgroup = EasyGroups::getInstance()->getGroupManager()->getAll()[EasyGroups::getInstance()->getGroupManager()->getDefault()]["tag"]; 
  $newgroup = EasyGroups::getInstance()->getGroupManager()->getAll()[self::$group["selected"]]["tag"]; 
  $form->setTitle("§7» Update Default Group » " . $newgroup);
  $form->setContent($plugin->getUtils()->replace(["{old_group}", "{new_group}"], [$oldgroup, $newgroup], $plugin->getMessages()->getNested("messages.default.CONFIRMATION_FORM"))); 
  $form->setButton1("§aCONFIRM");
  $form->setButton2("§cDECLINE");
  $form->sendToPlayer($player);
  return $form;  
 } 
 
}