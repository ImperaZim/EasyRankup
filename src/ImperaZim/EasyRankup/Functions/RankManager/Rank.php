<?php

namespace ImperaZim\EasyRankup\Functions\RankManager;

use pocketmine\utils\Config;
use pocketmine\player\Player;
use ImperaZim\EasyRankup\EasyRankup;

class Rank {
 
 public function getAll() : array {
  $config = new Config(EasyRankup::$instance->getDataFolder() . "ranks.yml");
  return $config->getAll()["ranks"];
 }
 
 public function getPermissionManager() : RankPermission {
  return new RankPermission();
 }
 
 public function getRankId($player) : int {
  return EasyRankup::$instance->getProvinder()->getRankId($player);
 }
 
 public function Upgrade(Player $player, Int $value) : bool {
  return EasyRankup::$instance->getProvinder()->addParamToRank($player, $value);
 }
 
 public function getTagByRank(String $rank) : string {
  return $this->getAll()[$rank]["tag"] ?? "no-rank";
 }
 
 public function getPermissionsByRank(String $rank) : array {
  return $this->getAll()[$rank]["permissions"] ?? [""];
 }
 
 public function getRankName(Player $player) : string {
  return $this->getRankInString($this->getRankId($player));
 }
 
 public function getRankNameById(Int $rankId) : string {
  return $this->getRankInString($rankId);
 }
 
 public function getPriceByRank(String $rank) : int {
  return $this->getAll()[$rank]["price"] ?? 0;
 } 
 
 public function getRankInString(Int $rankId) : string {
  $ranks = "";
  foreach ($this->getAll() as $rank => $data) { $ranks .= "$rank:"; }
  if(!isset(explode(":", $ranks)[$rankId])) return "no-rank";
  return (string) explode(":", $ranks)[$rankId];
 }  
 
 public function getRankInNumeric(String $rank) : int {
  $rankId = 0;
  foreach ($this->getAll() as $rankname => $data) {
   if ($rank == $rankname) return $rankId;
   $rankId = $rankId + 1;
  }
  return 0;
 } 
 
 public function isLastRank(Player $player) : bool {
  foreach($this->getAll() as $rank => $data) { $rankId = $rank; }
  return $this->getRankName($player) == $rankId;
 }
 
}
