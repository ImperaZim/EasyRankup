<?php

namespace imperazim\easyrankup\rank;

use imperazim\easyrankup\Loader;

class RankFactory {
  
  public static function getAll() : array {
    return Loader::getInstance()->ranks ?? [];
  }
  
  public static function registerAll(array $ranks) : void {
    foreach (array_keys($ranks) as $name) {
      $factory = new self();
      $factory->register($name, new Rank($ranks[$name]));
    }
  }
  
  public function register(string $name, Rank $rank) : void {
    Loader::getInstance()->ranks[$name] = $rank;
  }
  
}