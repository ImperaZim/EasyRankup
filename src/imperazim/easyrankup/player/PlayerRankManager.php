<?php

namespace imperazim\easyrankup\player;

use imperazim\easyrankup\Loader;
use imperazim\easyrankup\rank\Rank;
use imperazim\easyrankup\rank\RankManager;
use imperazim\easyrankup\provider\database\types\DatabaseProvider;

class PlayerRankManager {

  private Loader $plugin;
  private ?string $username;
  private ?Provider $provider;

  public function __construct(string $username) {
    $this->username = $username;
    $this->plugin = Loader::getInstance();
    $this->provider = $this->plugin->getDatabaseProvider();
  }

  public function getRank() : ?Rank {
    return RankManager::getRankById($this->getRankId());
  }

  public function upRankId() : void {
    $this->provider->setValue($this->username, 'rankid', $this->getRankId() + 1);
  }

  protected function getRankId() : int {
    return $this->provider->getValue($this->username, 'rankid');
  }

}