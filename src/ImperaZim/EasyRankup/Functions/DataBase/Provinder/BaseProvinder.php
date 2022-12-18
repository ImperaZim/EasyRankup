<?php

namespace ImperaZim\EasyRankup\Functions\DataBase\Provinder; 

use pocketmine\player\Player; 

interface BaseProvinder {
 
 public function table();
 
 public function CreateTable() : void;
 
 public function exist(Player $player) : bool;
 
 public function createProfile(Player $player) : void;
 
 public function addParamToRank(Player $player, Int $value) : bool;
 
 
}
