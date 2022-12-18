<?php

namespace ImperaZim\EasyRankup\Functions\RankManager; 

use pocketmine\player\Player;

class RankManager extends Rank {
 
 public function __construct() {
 }
 
 public function getId(Player $player) : string {
  return $this->getRankId($player);
 }
 
 public function getRank(Player $player) : string {
  return $this->getRankName($player);
 }
 
 public function getTag(Player $player) : string {
  return $this->getTagByRank($this->getRank($player));
 }
 
 public function getPriceToUpgrade(Player $player) : Int {
  return $this->getPriceByRank($this->getRankNameById($this->getId($player) + 1));
 }
 
 public function getNextRank(Player $player) : string {
  return $this->getTagByRank($this->getRankNameById($this->getId($player) + 1));
 }
 
}