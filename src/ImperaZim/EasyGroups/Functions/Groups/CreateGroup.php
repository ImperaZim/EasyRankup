<?php

namespace ImperaZim\EasyGroups\Functions\Groups;

use pocketmine\Server;
use pocketmine\utils\Config;
use ImperaZim\EasyGroups\Loader;
use ImperaZim\EasyGroups\Utils\form\GroupForm;

class CreateGroup {

 public static function execute($player) {
  $plugin = self::getPlugin();
  $server = self::getServer();
  $form = GroupForm::createCustomForm(function($player, $data = null){
   if (is_null($data)) return true;
   $name = isset($data["name"]) ? $data["name"] : "Unknow";
   $tag = isset($data["tag"]) ? $data["tag"] : "Unknow";
   $type = $data["type"] == true ? "vip" : "normal";
   self::create($player, $name, $tag, $type);
  });
  
  $form->setTitle("§cEasyGroups §7 » Create Group");
  $form->addLabel("§b");
  $form->addInput("§7Group Name | Example: §ePlayer§r", "name", "Player", "name");
  $form->addInput("§7Group Tag | Example: §e[player]§r", "tag", "§e[player]", "tag");
  $form->addLabel("§7Group type | Example: [§eNORMAL§7/§eVIP§7]");
  $form->addToggle("", false, "type");
  $form->sendToPlayer($player);
  return $form; 
 }
 
 public static function create($player, String $group, String $prefix, $type) {
  $plugin = Loader::getInstance();
  $messagem = $plugin->getConfig();  
  $config = new Config($plugin->getDataFolder() . "groups.yml");
  $type = strtolower($type);
  if (!isset($config->getAll()[$group])) {
   if(!in_array($type, ["normal", "vip"])) {
   $player->sendMessage(Loader::getProcessedTags(["{prefix}", "{type}"], [$messagem->get("default.prefix"), $type], $messagem->getNested('commands.subcommands.createcommand.failed_type', false)));
    return true;
   }
   $player->sendMessage(Loader::getProcessedTags(["{prefix}", "{group}"], [$messagem->get("default.prefix"), $prefix], $messagem->getNested('commands.subcommands.createcommand.sucess', false)));
   if($type == "vip"){
    $permission = ["easygroups.colored.format"];
   }else{
    $permission = [];
   }
   $config = new Config($plugin->getDataFolder() . "groups.yml", Config::YAML, [
    "$group" => [
      "tag" => "{$prefix}", 
      "type" => "{$type}", 
      "chat" => "{$prefix}§r§f {name}: §7{message}", 
      "nametag" => "{$prefix} {name}",
      "permission" => $permission
    ]
   ]);
   $config->save();
  }else{
   $player->sendMessage(Loader::getProcessedTags(["{prefix}", "{group}"], [$messagem->get("default.prefix"), $prefix], $messagem->getNested('commands.subcommands.createcommand.failed', false)));
  }
 }
 
 public static function getPlugin() : Loader {
  return Loader::getInstance();
 }
 
 public static function getServer() : Server {
  return Server::getInstance();
 }

}
