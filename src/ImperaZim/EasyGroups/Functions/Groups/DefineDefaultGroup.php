<?php

namespace ImperaZim\EasyGroups\Functions\Groups;

use pocketmine\Server;
use pocketmine\utils\Config;
use ImperaZim\EasyGroups\Loader;
use ImperaZim\EasyGroups\Utils\Form\FormAPI;
use ImperaZim\EasyGroups\Functions\Groups\_Group; 
use ImperaZim\EasyGroups\Functions\Storage\SQLite3; 

class DefineDefaultGroup {

 public static $saved = [];

 public static function execute($player) {
  $plugin = self::getPlugin();
  $form = FormAPI::createCustomForm(function($player, $data = null){
   $plugin = self::getPlugin();
   if (is_null($data)) return true;
   $array = "";
   $groups = new Config($plugin->getDataFolder() . "groups.yml");
   foreach ($groups->getAll() as $group => $other){
    if($array == "") {
     $array .= "{$group}";
    }else{
     $array .= "|{$group}";
    }
   }
   $groups = explode("|", $array); 
   self::$saved["group"] = $groups[$data["group"]];
   self::define($player, $groups[$data["group"]]);
  });
  
  $array = "";
  $groups = new Config($plugin->getDataFolder() . "groups.yml");
  foreach ($groups->getAll() as $group => $data){
   if($array == "") {
    $array .= "§7{$group} => " . $groups->getAll()[$group]["tag"];
   }else{
    $array .= "|§7{$group} => " . $groups->getAll()[$group]["tag"];
   }
  }
  $groups = explode("|", $array);
  $form->setTitle("§cEasyGroups §7 » Update Default Group");
  $form->addLabel("§b");
  $form->addDropdown("§7Listed Groups (select max \"1\")", $groups, 0, "group");
  $form->sendToPlayer($player);
  return $form;  
 }
 
 public static function define($player, String $group) {
  $def = SQLite3::default();
  $plugin = Loader::getInstance();
  $messagem = $plugin->getConfig();  
  $messagem->set("default.group", $group);
  $messagem->save();
  $groups = new Config($plugin->getDataFolder() . "groups.yml");
  $tag = $groups->getAll()[$group]["tag"];
  $player->sendMessage(Loader::getProcessedTags(["{prefix}", "{group} "], [$plugin->getConfig()->get("default.prefix"), $tag], $messagem->getNested('commands.subcommands.setdefaultgroup.sucess', false)));
 }
 
 public static function getPlugin() : Loader {
  return Loader::getInstance();
 }

}  
