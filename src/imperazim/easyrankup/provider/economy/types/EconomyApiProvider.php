<?php

namespace imperazim\easyrankup\provider\economy\types;

use pocketmine\Server;
use pocketmine\plugin\Plugin;
use pocketmine\player\Player;
use imperazim\easyrankup\Loader;

final class EconomyApiProvider implements EconomyProvider {
  private ?Server $server;
  
  public ?String $name;
  public ?Plugin $economy;
  
  public function __construct(private Loader $plugin) {
    $this->name = 'EconomyAPI';
    $this->server = $plugin->getServer();
    $this->economy = $this->server->getPluginManager()->getPlugin($this->name);
  }

  public function getMoney(Player $player) : int|float {
    return $this->economy->myMoney($player->getName());
  }

  public function reduceMoney(Player $player, int|float $value) : bool {
    $this->economy->reduceMoney($player->getName(), $value);
  }
}