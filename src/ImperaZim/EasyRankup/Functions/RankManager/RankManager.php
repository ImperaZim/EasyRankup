<?php

namespace ImperaZim\EasyRankup\Functions\RankManager; 

use pocketmine\player\Player;

class RankManager extends Rank {
 
 public function __construct() { }
 
 public function getId(Player $player) : Int {
  return $this->getRankId($player);
 }
 
 public function getRank(Player $player) : string {
  return $this->getRankName($player);
 }
 
 public function getTag(Player $player) : string {
  return $this->getTagByRank($this->getRank($player));
 }
 
 public function getPriceToUpgrade(Player $player) : Int {
  $rank = $this->getId($player) + 1;
  return $this->getPriceByRank($this->getRankNameById($rank));
 }
 
 public function getNextRank(Player $player) : string {
  $rank = $this->getId($player) + 1;
  return $this->getTagByRank($this->getRankNameById($rank));
 }
 
}