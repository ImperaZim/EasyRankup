<?php

namespace imperazim\easyrankup\provider\economy\types;

use pocketmine\player\Player;
use imperazim\easyrankup\Loader;

interface DatabaseProvider {
  public function __construct(Loader $plugin);

  public function getMoney(Player $player) : int|float;

  public function reduceMoney(Player $player, int|float $value) : bool;
}