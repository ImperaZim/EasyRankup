<?php

namespace imperazim\easyrankup\provider\economy\types;

use pocketmine\Server;
use pocketmine\plugin\Plugin;
use pocketmine\player\Player;
use imperazim\easyrankup\Loader;
use cooldogedev\BedrockEconomy\api\BedrockEconomyAPI;

final class BedrockEconomyProvider implements EconomyProvider {
  private ?Server $server;

  public ?String $name;
  public ?Plugin $economy;

  public function __construct(private Loader $plugin) {
    $this->name = 'BedrockEconomy';
    $this->server = $plugin->getServer();
    $this->economy = $this->server->getPluginManager()->getPlugin($this->name);
  }

  public function getMoney(Player $player) : int|float {
    return BedrockEconomyAPI::legacy()->getPlayerBalance($player->getName());
  }

  public function reduceMoney(Player $player, int|float $value) : bool {
    $intMoney = (int) floor($value);
    BedrockEconomyAPI::legacy()->subtractFromPlayerBalance($player->getName(), $intMoney);
  }
}