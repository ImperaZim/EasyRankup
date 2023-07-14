<?php

namespace imperazim\easyrankup\rank;

use imperazim\easyrankup\Loader;
use imperazim\easyrankup\rank\Rank;
use pockermine\utils\SingletonTrait;

class RankManager {
  
  public static function getAll() : array {
    $plugin = Loader::getInstance();
    $config = new Config($plugin->getDataFolder() . 'ranks.yml');
    return $config->getAll()['ranks'] ?? [];
  }
  
  public static function getData(string $name) : array {
    return (new Config(Loader::getInstance()->getDataFolder() .
    'ranks.yml'))->getAll()['ranks'][$name] ?? [];
  }
  
  public static function getRankById(int $rankid) : ?Rank {
    return array_keys(RankFactory::getAll())[$rankid];
  }
  
}