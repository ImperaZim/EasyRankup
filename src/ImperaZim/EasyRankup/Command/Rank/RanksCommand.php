<?php

namespace ImperaZim\EasyRankup\Command\Rank;

use pocketmine\Server;
use pocketmine\player\Player;
use pocketmine\command\Command;
use pocketmine\plugin\PluginOwned;
use ImperaZim\EasyRankup\EasyRankup;
use ImperaZim\EasyRankup\PluginUtils;
use pocketmine\command\CommandSender;
use ImperaZim\EasyRankup\Forms\FormAPI;
use ImperaZim\EasyRankup\Task\RankUPTask; 
use ImperaZim\EasyEconomy\PluginUtils as EconomyUtils; 

class RanksCommand extends Command implements PluginOwned {
 
 public $data = [];

 public function __construct() {
  parent::__construct("ranks", "§7rank list!", null, []);
 }

 public function execute(CommandSender $player, String $commandLabel, array $args) : bool {
  $plugin = $this->getOwningPlugin();
  if (!$player instanceof Player) {
   $plugin->getLogger()->error("This command can only be used in the game"); 
   return true;
  }
  if (isset($plugin->tasks[$player->getName()])) {
   $player->sendMessage(PluginUtils::convertString([], [], $plugin->getMessages()->getNested("commands.rankup.notify.duplicate")));
   return true;
  }
  $this->RankList($player);
  return true;
 }
 
 public function RankList(Player $player) {
  $form = FormAPI::createSimpleForm(function($player, $data = null){
   $plugin = $this->getOwningPlugin(); 
   if (is_null($data)) {
    return true;
   }
   
   $ranks = $plugin->getRankManager();  
   
   if ($data != null) {
    $this->rankDetails($player, $data);
    return;
   }
  });
  
  $plugin = $this->getOwningPlugin(); 
  $ranks = $plugin->getRankManager();
  
  $form->setTitle("§eRank's List");
  $form->setContent("");
  foreach ($ranks->getAll() as $rank => $data) {
   $price = number_format($data["price"], 0, "",  ".");
   $price_converted = EconomyUtils::convertCurrency($data["price"]);
   if ($ranks->getRankInNumeric($rank) < $ranks->getRankId($player)) {
    $price = PluginUtils::convertString([], [], $plugin->getMessages()->getNested("forms.ranks.SURPASSED_RANK")); 
    $price_converted = "#";
   }
   if ($ranks->getRankInNumeric($rank) == $ranks->getRankId($player)) {
    $price = PluginUtils::convertString([], [], $plugin->getMessages()->getNested("forms.ranks.CURRENT_RANK"));
    $price_converted = "#";
   }
   $title = PluginUtils::convertString(["{line_up}", "{price}", "{price_converted}"], ["\n", $price, $price_converted], $data["title_form"]);
   $title = PluginUtils::convertString(["(#)"], [""], $title);
   $icon = explode("=", $data["icon_form"]);
   $icon_type = $icon[0] == "path" ? 0 : 1;
   $icon_link = $icon[1] ?? "";
   $form->addButton($title, $icon_type, $icon_link, $rank);
  } 
  $form->sendToPlayer($player);
  return $form;  
 }
 
 public function rankDetails(Player $player, String $rank) {
  $this->data["rank"] = $rank;
  $form = FormAPI::createSimpleForm(function($player, $data = null){
   $plugin = $this->getOwningPlugin(); 
   if (is_null($data)) {
    return true;
   }
   
   $ranks = $plugin->getRankManager();  
   
   if ($data == "back") {
    return $this->RankList($player);
   }
   
   if ($data == "rankup") {
    $rank = $this->data["rank"];
    
    if (isset($plugin->tasks[$player->getName()])) {
     $player->sendMessage(PluginUtils::convertString([], [], $plugin->getMessages()->getNested("commands.rankup.notify.duplicate")));
     return;
    }
   
    $plugin->tasks[$player->getName()] = $player->getName();
    
    if ($ranks->getRankInNumeric($rank) <= $ranks->getRankId($player)) {
     return;
    }
    if ($ranks->isLastRank($player)) {
     $player->sendMessage(PluginUtils::convertString([], [], $plugin->getMessages()->getNested("commands.rankup.notify.in_last_rank"))); 
     return;
    }
    $rankId = $ranks->getRankInNumeric($rank) - $ranks->getRankId($player);
    $plugin->getScheduler()->scheduleRepeatingTask(new RankUPTask($player, $rankId), 20); 
    return;
   }
  });
  
  $plugin = $this->getOwningPlugin(); 
  $ranks = $plugin->getRankManager();
  $form->setTitle(PluginUtils::convertString(["{selected_rank}"], [str_replace(["[", "]"], [""], $ranks->getTagByRank($rank))], $plugin->getMessages()->getNested("forms.ranks.rank_details.title")));
  
  $calc = 0;
  $price = (int)  0;
  $rankId = $ranks->getRankId($player);
  $rankTo = $ranks->getRankInNumeric($rank) - $rankId; 
  for ($id = 1; $id <= $rankTo; $id++) {
   $id_c = $rankId + $id;
   $calc = $calc + $ranks->getPriceByRank($ranks->getRankNameById($id_c));
  }
  
  if ($ranks->getRankInNumeric($rank) > $rankId) { 
   $price = (int) $calc;
  }
  
  $price_converted = EconomyUtils::convertCurrency($price); 
  $price = number_format($price, 0, "",  ".");
   
  if ($ranks->getRankInNumeric($rank) < $rankId) {
   $price = PluginUtils::convertString([], [], $plugin->getMessages()->getNested("forms.ranks.SURPASSED_RANK")); 
   $price_converted = "0";
  }
  
  if ($ranks->getRankInNumeric($rank) == $rankId) {
   $price = PluginUtils::convertString([], [], $plugin->getMessages()->getNested("forms.ranks.CURRENT_RANK"));
   $price_converted = "0";
  }
  
  $content = "";
  foreach ($plugin->getMessages()->getNested("forms.ranks.rank_details.content") as $line) {
   $content .= "\n" . $line;
  }
  if ($ranks->getRankInNumeric($rank) < $rankId) {
   $form->setContent(PluginUtils::convertString(["{rank}", "{rank_with_bars}", "{price}", "{price_converted}", "{player_rank_with_bars}", "{selected_rank_with_bars}", "{price_to_jump}", "{price_to_jump_converted}", "{lineup}"], [str_replace(["[", "]"], [" "], $ranks->getTagByRank($rank)), $ranks->getTagByRank($rank), number_format($ranks->getPriceByRank($rank)), EconomyUtils::convertCurrency($ranks->getPriceByRank($rank)), $ranks->getTag($player), $ranks->getTagByRank($rank), $price, $price_converted, "\n"], $plugin->getMessages()->getNested("forms.ranks.rank_details.SURPASSED_RANK_DETAILS")));
  }elseif ($ranks->getRankInNumeric($rank) == $rankId) {
   $form->setContent(PluginUtils::convertString(["{rank}", "{rank_with_bars}", "{price}", "{price_converted}", "{player_rank_with_bars}", "{selected_rank_with_bars}", "{price_to_jump}", "{price_to_jump_converted}", "{lineup}"], [str_replace(["[", "]"], [" "], $ranks->getTagByRank($rank)), $ranks->getTagByRank($rank), number_format($ranks->getPriceByRank($rank)), EconomyUtils::convertCurrency($ranks->getPriceByRank($rank)), $ranks->getTag($player), $ranks->getTagByRank($rank), $price, $price_converted, "\n"], $plugin->getMessages()->getNested("forms.ranks.rank_details.CURRENT_RANK_DETAILS")));
  }else{
   $form->setContent(PluginUtils::convertString(["{rank}", "{rank_with_bars}", "{price}", "{price_converted}", "{player_rank_with_bars}", "{selected_rank_with_bars}", "{price_to_jump}", "{price_to_jump_converted}", "{lineup}"], [str_replace(["[", "]"], [" "], $ranks->getTagByRank($rank)), $ranks->getTagByRank($rank), number_format($ranks->getPriceByRank($rank)), EconomyUtils::convertCurrency($ranks->getPriceByRank($rank)), $ranks->getTag($player), $ranks->getTagByRank($rank), $price, $price_converted, "\n"], $content));
  }
  
  if ($ranks->getRankInNumeric($rank) > $rankId) { 
   $icon = explode("=", $plugin->getMessages()->getNested("forms.ranks.rank_details.buttons.rankup.icon"));
   $icon_type = $icon[0] == "path" ? 0 : 1;
   $icon_link = $icon[1] ?? "";
   $form->addButton(PluginUtils::convertString(["{lineup}"], ["\n"], $plugin->getMessages()->getNested("forms.ranks.rank_details.buttons.rankup.title")), $icon_type, $icon_link, "rankup");
  }
  
  $icon = explode("=", $plugin->getMessages()->getNested("forms.ranks.rank_details.buttons.back.icon"));
  $icon_type = $icon[0] == "path" ? 0 : 1;
  $icon_link = $icon[1] ?? "";
  $form->addButton(PluginUtils::convertString(["{lineup}"], ["\n"], $plugin->getMessages()->getNested("forms.ranks.rank_details.buttons.back.title")), $icon_type, $icon_link, "back");
  
  $form->sendToPlayer($player);
  return $form;   
 }
 
 public function getOwningPlugin() : EasyRankup {
  return EasyRankup::getInstance();
 }
 
} 
