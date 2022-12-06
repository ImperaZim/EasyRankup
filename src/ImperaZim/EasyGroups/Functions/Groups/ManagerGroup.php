<?php

namespace ImperaZim\EasyGroups\Functions\Groups;

use pocketmine\utils\Config;
use ImperaZim\EasyGroups\Loader;
use ImperaZim\EasyGroups\Utils\form\FormAPI as GroupForm;

class ManagerGroup {
 
 public static $saved = [];

 public static function execute($player, String $group) {
  $plugin = self::getPlugin();
  $config = new Config($plugin->getDataFolder() . "groups.yml");
  
  if(!isset($config->getAll()[$group])) return true;
  self::$saved["group"] = $group;
  $form = GroupForm::createCustomForm(function($player, $data = null) {
   if (is_null($data)) return true;
   $group = self::$saved["group"];
   $plugin = self::getPlugin();
   $config = new Config($plugin->getDataFolder() . "groups.yml"); 
   $config->setNested("$group.tag", $data["tag"]);
   $config->setNested("$group.chat", $data["chat"]);
   $config->setNested("$group.type", $data["type"]);
   $config->setNested("$group.nametag", $data["nametag"]);
   $config->setNested("$group.permission", $data["permission"]);
   $config->save(); 
  });
  $form->setTitle("§b@{$group}'s §7manager");
  $form->addInput("§7Group tag (only text)", "Group name", $config->getAll()[$group]["tag"], "tag");
  $form->addInput("§7Group tag (only normal or vip)", "Group type", $config->getAll()[$group]["type"], "type");
  $form->addInput("§7Group chat preset (only text)", "Group chat", $config->getAll()[$group]["chat"], "chat");
  $form->addInput("§7Group nametag preset (only text)", "Group nametag", $config->getAll()[$group]["nametag"], "nametag");
  $form->addInput("§7Group permission (perm.use|perm2.use)", "Group permissions", $config->getAll()[$group]["permission"], "permission");
  $form->sendToPlayer($player);
  return $form; 
 }
 
 public static function getPlugin() : Loader {
  return Loader::getInstance();
 }


}  
