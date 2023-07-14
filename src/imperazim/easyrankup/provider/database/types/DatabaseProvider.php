<?php

namespace imperazim\easyrankup\provider\database\types;

use imperazim\easyrankup\Loader;

interface DatabaseProvider {
  public function __construct(Loader $plugin);

  public function createTable(): void;

  public function exists(string $player): bool;

  public function create(string $player): bool;

  public function getValue(string $player, string $k): mixed;

  public function addValue(string $player, string $k, mixed $v): bool|int;
}