<?php

namespace ImperaZim\EasyGroups\Functions\Groups;

use pocketmine\utils\Config;
use ImperaZim\EasyGroups\Loader;

class ListGroup {

 public static function execute($player) {
  $vips = "";
  $normals = "";
  $plugin = Loader::getInstance();
  $messagem = $plugin->getConfig();  
  $config = new Config($plugin->getDataFolder() . "groups.yml");
  foreach ($config->getAll() as $group => $data) {
   $type = strtolower($config->getAll()[$group]["type"]);
   if($type == "vip") {
    $vips .= ", §7{$group}";
   }
   if($type == "normal") {
    $normals .= ", §7{$group}";
   }
  }
  $vips = $vips == "" ? " " : substr("{$vips}§r§7.", 2); 
  $normals = $normals == "" ? " " : substr("{$normals}§r§7.", 2); 
  $player->sendMessage(Loader::getProcessedTags(["{prefix}", "{normals}", "{vips}"], [$messagem->get("default.prefix"), $normals, $vips], $messagem->getNested('commands.subcommands.listcommand.preset', false))); 
 }


} 