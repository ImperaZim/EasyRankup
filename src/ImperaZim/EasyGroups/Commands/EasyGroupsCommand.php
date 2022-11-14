<?php

namespace ImperaZim\EasyGroups\Commands;

use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\player\Player;
use pocketmine\command\Command;
use ImperaZim\EasyGroups\Loader;
use pocketmine\plugin\PluginOwned;
use pocketmine\command\CommandSender;
use ImperaZim\EasyGroups\Functions\Groups\ListGroup;
use ImperaZim\EasyGroups\Functions\Groups\CreateGroup;
use ImperaZim\EasyGroups\Functions\Groups\UpdateGroup;
use ImperaZim\EasyGroups\Functions\Groups\DeleteGroup;
use ImperaZim\EasyGroups\Functions\Permission\addPerm;
use ImperaZim\EasyGroups\Functions\Permission\removePerm;
use ImperaZim\EasyGroups\Functions\Groups\ManagerGroup;
use ImperaZim\EasyGroups\Functions\Groups\DefineDefaultGroup;

class EasyGroupsCommand extends Command implements PluginOwned {

 public function __construct() {
  parent::__construct("easygroups", "§7Groups manager!", null, ["eg", "tag", "group"]);
  $this->setPermission("easygroups.operator.use");
 }

 public function execute(CommandSender $player, string $commandLabel, array $args) : bool {
  if (!$player instanceof Player) {
   self::getServer()->getLogger()->error("This command can only be used in the game");
   return true;
  }
  if(!$player->hasPermission("easygroups.operator.use")){
   $player->sendMessage(Loader::getProcessedTags(["{prefix}"], [Loader::getInstance()->getConfig()->get("default.prefix")], Loader::getInstance()->getConfig() ->getNested('commands.no_permission', false))); 
   return true;
  }
  self::subcommands($player, $args);
  return true;
 }

 public function getOwningPlugin() : Loader {
  return Loader::getInstance();
 }

 public static function getServer() : Server {
  return Server::getInstance();
 }

 public static function subcommands($player, array $args) {
  $plugin = Loader::getInstance();
  $config = $plugin->getConfig();
  if (isset($args[0])) {
   if (in_array($args[0], ["setar", "set"])) {
    if(!isset($args[2])) {
     $player->sendMessage(Loader::getProcessedTags(["{prefix}"], [$plugin->getConfig()->get("default.prefix")], $config->getNested('commands.subcommands.setcommand.help_message', false)));
     return;
    }
    $target = Server::getInstance()->getPlayerExact($args[1]); 
    if(!$target instanceof Player) {
     $player->sendMessage(Loader::getProcessedTags(["{prefix}", "{target}"], [$plugin->getConfig()->get("default.prefix"), $args[1]], $config->getNested('commands.subcommands.setcommand.offline_player', false))); 
     return;
    } 
    UpdateGroup::execute($player, $target, $args[2]);
    return;
   }
   if (in_array($args[0], ["help", "ajuda"])) {
    $player->sendMessage("§l§eGROUPS§r §7EasyGroups commands: \n §e-> §7/easygroups help \n §e-> §7/easygroups list \n §e-> §7/easygroups edit (name) \n §e-> §7/easygroups create \n §e-> §7/easygroups delete \n §e-> §7/easygroups set (player) (name) \n §e-> §7/easygroups addperm \n §e-> §7/easygroups removeperm");
    return;
   }
   if (in_array($args[0], ["lista", "list"])) { 
    ListGroup::execute($player);
    return; 
   }
   if (in_array($args[0], ["editar", "edit"])) {
    if(!isset($args[1])) {
     $player->sendMessage(Loader::getProcessedTags(["{prefix}"], [$plugin->getConfig()->get("default.prefix")], $config->getNested('commands.subcommands.editcommand.help_message', false)));
     return;
    }
    $player->sendMessage(Loader::getProcessedTags(["{prefix}"], [$plugin->getConfig()->get("default.prefix")], "{prefix} §7SOON!!!"));
    return; 
    ManagerGroup::execute($player, (string) $args[1]);
    return; 
   }
   if (in_array($args[0], ["criar", "create"])) {
    CreateGroup::execute($player);
    return;
   }
   if (in_array($args[0], ["deletar", "delete"])) {
    DeleteGroup::execute($player);
    return;
   }
   if (in_array($args[0], ["setdefault"])) {
    DefineDefaultGroup::execute($player);
    return;
   } 
   if (in_array($args[0], ["addperm"])) {
    addPerm::execute($player);
    return;
   }
   if (in_array($args[0], ["removeperm"])) {
    removePerm::execute($player);
    return;
   }
   $player->sendMessage(Loader::getProcessedTags(["{prefix}"], [$plugin->getConfig()->get("default.prefix")], $config->getNested('commands.help_message', false))); 
    return;
  }else{
    $player->sendMessage(Loader::getProcessedTags(["{prefix}"], [$plugin->getConfig()->get("default.prefix")], $config->getNested('commands.help_message', false))); 
  }
 }

}
