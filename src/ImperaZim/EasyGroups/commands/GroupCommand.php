<?php

namespace ImperaZim\EasyGroups\commands;

use pocketmine\Server;
use pocketmine\player\Player;
use pocketmine\command\Command;
use pocketmine\plugin\PluginOwned;
use ImperaZim\EasyGroups\EasyGroups;
use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use ImperaZim\EasyGroups\forms\presets\CreateGroupForm;
use ImperaZim\EasyGroups\forms\presets\DeleteGroupForm;
use ImperaZim\EasyGroups\forms\presets\PlayerGroupForm;
use ImperaZim\EasyGroups\forms\presets\UpdateGroupForm;
use ImperaZim\EasyGroups\forms\presets\DefaultGroupForm;
use ImperaZim\EasyGroups\forms\presets\addPermissionForm;
use ImperaZim\EasyGroups\forms\presets\RemovePermissionForm;
use ImperaZim\EasyGroups\forms\presets\PlayerPermissionForm;
class GroupCommand extends Command implements PluginOwned {

 public function __construct() {
  parent::__construct("groups", "§7EasyGroups command!", null, ["tag", "easygroups"]);
 }

 public function execute(CommandSender $player, String $commandLabel, array $args) : bool { 
  $this->subcommands($player, $args);
  return true;
 }
 
 public function subcommands(CommandSender $player, array $args) {
  if (isset($args[0])) {
   $command = strtolower($args[0]);
   $plugin = $this->getOwningPlugin();
   
   if (in_array($command, ["help"])) {
    if (!$player instanceof ConsoleCommandSender) {
     if (!$player->hasPermission("easygroups.command.help")) {
      return $player->sendMessage($plugin->getUtils()->replace([], [], $plugin->getMessages()->getNested("messages.error.WITHOUT_PERMISSION")));
     }
    }
    if ((!isset($args[1])) || $args[1] == "1") { 
     return $player->sendMessage($this->getOwningPlugin()->getUtils()->replace(["§e ->"], ["\n §e ->"], "{prefix} §7EasyGroups Commands: [1/2] §e -> §7/groups help §e -> §7/groups create §e -> §7/groups delete §e -> §7/groups update"));
    }
    if ((isset($args[1])) || $args[1] == "2") { 
     return $player->sendMessage($this->getOwningPlugin()->getUtils()->replace(["§e ->"], ["\n  §e ->"], "{prefix} §7EasyGroups Commands: [2/2] §e -> §7/groups set §e -> §7/groups setdefault §e -> §7/groups permission [add/remove]"));  
    }
    return $player->sendMessage($this->getOwningPlugin()->getUtils()->replace([], [], "{prefix} §7Use /groups help [1/2]")); 
   } #
   
   if (!$player instanceof Player) {
    $plugin->getLogger()->error("This command can only be used in the game"); 
    return;
   }
   
   if (in_array($command, ["create", "criar"])) {
    return CreateGroupForm::sendDetails($player);
   } #
   if (in_array($command, ["update", "editar"])) {
    return UpdateGroupForm::selectGroup($player);
   } #
   if (in_array($command, ["delete", "deletar"])) {
    return DeleteGroupForm::selectGroup($player);
   } #
   if (in_array($command, ["setdefault"])) {
    return DefaultGroupForm::selectGroup($player);
   } #
   if (in_array($command, ["set", "setar"])) {
    return PlayerGroupForm::sendDetails($player);
   } #
   if (in_array($command, ["perm", "permission"])) {
    if (isset($args[1])) {
     if (in_array(strtolower($args[1]), ["add", "adcionar"])) {
      return addPermissionForm::selectGroup($player);
     }
     if (in_array(strtolower($args[1]), ["remove", "remover"])) {
      return RemovePermissionForm::selectGroup($player);
     }
     if (in_array(strtolower($args[1]), ["user", "player"])) {
      return PlayerPermissionForm::sendDetail($player);
     }
    } 
    return $player->sendMessage($plugin->getUtils()->replace([], [], "{prefix} §7Use /groups permission \"add | remove | user\""));
   } #
 }
  return $player->sendMessage($this->getOwningPlugin()->getUtils()->replace([], [], "{prefix} §7Use /groups help"));
 }
 
 public function getOwningPlugin() : EasyGroups {
  return EasyGroups::getInstance();
 }
 
} 
