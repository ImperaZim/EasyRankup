<?php

namespace ImperaZim\EasyGroups\forms\presets;

use pocketmine\player\Player;
use ImperaZim\EasyGroups\EasyGroups;

class CreateGroupForm extends \ImperaZim\EasyGroups\forms\FormAPI {
 
 const NO_PERMISSION = 0;
 public static $group = [];
 
 public static function sendDetails(Player $player) {
  $plugin = EasyGroups::getInstance();
  $p1 = !$player->hasPermission("easygroups.command") ? 0 : 1;
  $p2 = !$player->hasPermission("easygroups.command.create") ? 0 : 1;
  $p3 = $p1 + $p2;
  if ($p3 == self::NO_PERMISSION) {
   return $player->sendMessage($plugin->getUtils()->replace([], [], $plugin->getMessages()->getNested("messages.error.WITHOUT_PERMISSION")));
  } 
  $form = self::sendCustomForm(function($player, $data = null){
   if ($data == null) return;
   $name = $data["name"] ?? "null";
   $tag = $data["tag"] ?? "null";
   $type = $data["type"] == 0 ? "normal" : "vip";
   
   self::$group["name"] = $name;
   self::$group["tag"] = $tag;
   self::$group["type"] = $type;
   self::setPresets($player);
  });
  $form->setTitle("§eEasyGroups §7» Create Group");
  $form->addLabel("§b", "space");
  $form->addInput("§7Group Name | Example: §ePlayer§r", " name", "Player", "name");
  $form->addInput("§7Group Tag | Example: §e[player]§r", " tag",  "§e[player]", "tag");
  $form->addDropdown("§7Group Type | Types", ["normal", "vip"], 0, "type");
  $form->sendToPlayer($player);
  return $form;
 }
 
 public static function setPresets(Player $player) {
  $form = self::sendCustomForm(function($player, $data = null){
   $plugin = EasyGroups::getInstance();
   if ($data == null) return self::sendDetails($player); 
   
   $nametag = $data["nametag"] ?? "{tag} {player}";
   $chat_prefix = $data["prefix"] ?? "{tag} {player}: §7{message}";  
   if ($plugin->getGroupManager()->create($player, self::$group["name"], self::$group["tag"], self::$group["type"], $nametag, $chat_prefix, [])) {
    $player->sendMessage($plugin->getUtils()->replace(["{group}"], [self::$group["tag"]], $plugin->getMessages()->getNested("messages.create.SUCCESSFULLY_CREATE")));
   }
  });
  
  $tag = self::$group["tag"];
  $form->setTitle("§7Create Group §7» {$tag}");
  $form->addLabel("§b", "space");
  $form->addInput("§7Group Nametag | Preset ", " nametag", "§7{tag} {player}", "nametag");
  $form->addInput("§7Group Chat Message | Preset ", " chat message", "§7{tag} {player}:§r§7 {message}", "prefix");
  $form->sendToPlayer($player);
  return $form;
 }
 
}