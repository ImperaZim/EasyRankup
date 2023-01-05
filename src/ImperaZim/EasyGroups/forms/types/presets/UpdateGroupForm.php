<?php

namespace ImperaZim\EasyGroups\forms\presets;

use pocketmine\player\Player;
use ImperaZim\EasyGroups\EasyGroups;

class UpdateGroupForm extends \ImperaZim\EasyGroups\forms\FormAPI {
 
 const NO_PERMISSION = 0;
 public static $group = []; 
 
 public static function selectGroup(Player $player) {
  $plugin = EasyGroups::getInstance();
  $p1 = !$player->hasPermission("easygroups.command") ? 0 : 1;
  $p2 = !$player->hasPermission("easygroups.command.update") ? 0 : 1;
  $p3 = $p1 + $p2;
  if ($p3 == self::NO_PERMISSION) {
   return $player->sendMessage($plugin->getUtils()->replace([], [], $plugin->getMessages()->getNested("messages.error.WITHOUT_PERMISSION")));
  } 
  $form = self::sendCustomForm(function($player, $data = null){
   $plugin = EasyGroups::getInstance();
   if ($data == null) return; 
   $groups = "";
   foreach ($plugin->getGroupManager()->getAll() as $group => $dat) {
    if ($groups != "") $groups .= "^$group"; 
    if ($groups == "") $groups .= "$group";
   }
   self::$group["selected"] = explode("^", $groups)[$data["group"]];
   self::editSettings($player);
  });
  $form->setTitle("§eEasyGroups§7 » Update Group");
  $groups = "";
  foreach (EasyGroups::getInstance()->getGroupManager()->getAll() as $group => $dat) {
   if ($groups != "") $groups .= "^§7{$group} => {$dat['tag']}";
   if ($groups == "") $groups .= "§7{$group} => {$dat['tag']}";
  } 
  $form->addDropdown("§7Select Group", explode("^", $groups), 0, "group"); 
  $form->sendToPlayer($player);
  return $form;
 }
 
 public static function editSettings(Player $player) {
  $plugin = EasyGroups::getInstance();
  $form = self::sendCustomForm(function($player, $data = null){
   $plugin = EasyGroups::getInstance();
   if ($data == null) return;
   self::$group["tag"] = $data["tag"] ?? "";
   self::$group["type"] = $data["type"] == 0 ? "normal" : "vip";
   self::$group["chat"] = $data["chat"] ?? "";
   self::$group["nametag"] = $data["nametag"] ?? "";
   self::sendConfirmation($player);
  });
  $group = self::$group["selected"];  
  $groups = $plugin->getGroupManager()->getAll();
  
  $tag = $groups[$group]["tag"] ?? "";
  $type = $groups[$group]["type"] == "normal" ? 0 : 1;
  $chat = $groups[$group]["chat"] ?? "";
  $nametag = $groups[$group]["nametag"] ?? "";
  
  $form->setTitle("§7Update Group » §e" . self::$group["selected"]);
  $form->addInput("§7Group Tag | (in text) §r", " tag", $tag, "tag");
  $form->addDropdown("§7Group Type | Types", ["normal", "vip"], $type, "type");
  $form->addInput("§7Group Nametag | Preset ", " nametag", $nametag, "nametag");
  $form->addInput("§7Group Chat Message | Preset ", " chat message", $chat, "chat");
  $form->sendToPlayer($player);
  return $form; 
 }
 
 public static function sendConfirmation(Player $player) {
  $plugin = EasyGroups::getInstance();
  $form = self::sendQuestionForm(function($player, $data = null) {
   $plugin = EasyGroups::getInstance();
   if ($data == null || $data == false) self::selectGroup($player);
   $name = self::$group["selected"]; 
   $manager = $plugin->getGroupManager();
   
   $config = $manager->getConfig();
   $config->setNested("{$name}.tag", self::$group["tag"]);
   $config->setNested("{$name}.type", self::$group["type"]);
   $config->setNested("{$name}.chat", self::$group["chat"]);
   $config->setNested("{$name}.nametag", self::$group["nametag"]);
   $config->save(); 
   $player->sendMessage($plugin->getUtils()->replace(["{group}"], [self::$group["tag"]], $plugin->getMessages()->getNested("messages.edit.SUCCESSFULLY_UPDATE")));
  });
  $group = self::$group["selected"];  
  $form->setTitle("Update Group » " . $group);
  $tag = $plugin->getGroupManager()->getAll()[$group]["tag"]; 
  $form->setContent($plugin->getUtils()->replace(["{group}"], [$tag], $plugin->getMessages()->getNested("messages.edit.CONFIRMATION_FORM"))); 
  $form->setButton1("§aCONFIRM");
  $form->setButton2("§cDECLINE");
  $form->sendToPlayer($player);
  return $form;  
 } 
 
}