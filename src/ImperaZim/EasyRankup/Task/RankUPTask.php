<?php

namespace ImperaZim\EasyRankup\Task;

use pocketmine\Server;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use ImperaZim\EasyRankup\EasyRankup;
use ImperaZim\EasyRankup\PluginUtils;
use pocketmine\world\sound\XpCollectSound; 
use pocketmine\scheduler\CancelTaskException;
use ImperaZim\EasyRankup\Dependence\EasyEconomy;
use pocketmine\entity\animation\TotemUseAnimation; 

class RankUPTask extends Task {

 public $data = [];

 public function __construct(Player $player, Int $rankto) {
  $this->data["timer"] = 15;
  $this->data["plugin"] = EasyRankup::getInstance();
  $this->data["manager"] = $this->data["plugin"]->getRankManager();
  
  $manager = $this->data["manager"];
  
  $this->data["player"] = $player;
  $this->data["rankid"] = $manager->getRankId($player);
  $this->data["rankto"] = $rankto;
  $this->data["calc_rank"] = $manager->getRankId($player) + $rankto;
  $this->data["rank"] = $manager->getRankNameById($this->data["calc_rank"]);
  $this->data["tag"] = $manager->getTagByRank($this->data["rank"]);
  $this->data["price"] = 0;
 }

 public function onRun() : void {
  $player = $this->data["player"];
  $plugin = $this->data["plugin"];
  
  $this->data["calc"] = 0;
  for ($id = 1; $id <= $this->data["rankto"]; $id++) {
   $id_c = $this->data["rankid"] + $id;
   $this->data["calc"] = $this->data["calc"] + $this->data["manager"]->getPriceByRank($this->data["manager"]->getRankNameById($id_c));
   //$player->sendMessage("$" . $this->data["calc"]);
  } 
  
  $this->data["price"] = $this->data["calc"];
  
  if ($this->data["timer"] == 15) {
   $player->sendMessage(PluginUtils::convertString([], [], $plugin->getMessages()->getNested("commands.rankup.notify.confirm"))); 
  }
  
  $this->data["timer"] = $this->data["timer"] - 1;
  $this->data["money"] = PluginUtils::convertCurrency($this->data["price"]);
  
  if (!$player instanceof Player) {
   throw new CancelTaskException();
  } 
  
  $this->data["money"] = $this->data["price"] > EasyEconomy::getMoney($player)
  ? "§c" . $this->data['money'] . " [!]"
  : "§a" . $this->data['money'] . " [ ]";
  
  $player->sendPopup("§e[{$this->data['timer']}] " . PluginUtils::convertString(["{rank}", "{price}"], [$this->data['tag'], $this->data["money"]], $plugin->getMessages()->getNested("commands.rankup.notify.countdown"))); 
  
  if ($this->data["timer"] == 0) {
   $player->sendPopup(""); 
   $player->sendMessage(PluginUtils::convertString([], [], $plugin->getMessages()->getNested("commands.rankup.notify.timeout")));
   throw new CancelTaskException();
  }
  
  $this->data["money"] = str_replace([" [ ]"], [""], $this->data["money"]);
  
  if ($player->isSneaking()) {
   $player->sendPopup("");
   if ($this->data["price"] > EasyEconomy::getMoney($player)) {
    $player->sendMessage(PluginUtils::convertString([], [], $plugin->getMessages()->getNested("commands.rankup.notify.insufficient_money")));
   }
   if ($this->data["price"] <= EasyEconomy::getMoney($player)) {
    $this->data["manager"]->Upgrade($player, $this->data["rankto"]);
    EasyEconomy::reduceMoney($player, $this->data["price"]);
    $this->data["manager"]->getPermissionManager()->UpdatePermissions($player);
    $player->sendMessage(PluginUtils::convertString(["{rank}", "{money}"], [$this->data['tag'], $this->data['money']], $plugin->getMessages()->getNested("commands.rankup.notify.successfully_message")));
    $message = explode(":", PluginUtils::convertString(["{rank}"], [$this->data['tag']], $plugin->getMessages()->getNested("commands.rankup.notify.successfully_title")));
    $player->sendTitle($message[0], $message[1]); 
    
    $player->broadcastAnimation(new TotemUseAnimation($player)); 
    $player->getWorld()->addSound($player->getPosition(), new XpCollectSound(), [$player]); 
   }
   
   throw new CancelTaskException();
  }
  
 }
 
 public function onCancel() : void {
  $player = $this->data["player"]; 
  $plugin = $this->data["plugin"]; 
  if (isset($plugin->tasks[$player->getName()])) {
   unset($plugin->tasks[$player->getName()]);
  }
 }
 
} 
