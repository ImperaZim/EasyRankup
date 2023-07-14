<?php

namespace imperazim\easyrankup\provider\database;

use imperazim\easyrankup\Loader;
use imperazim\easyrankup\provider\database\types\MysqlProvider;
use imperazim\easyrankup\provider\database\types\SqliteProvider;
use imperazim\easyrankup\provider\database\types\DatabaseProvider;

final class DatabaseProviderManager {
  private Loader $plugin;

  public function __construct(Loader $plugin) {
    $this->plugin = $plugin;
    $this->plugin->providers = [];
    $this->register('mysql', new MysqlProvider($plugin));
    $this->register('sqlite', new SqliteProvider($plugin));
  }

  public function register(string $name, DatabaseProvider $provider): void {
    $this->providers['database'][strtolower($name)] = $provider;
  }

  public function getProvider(): string {
    $cfg = $this->plugin->getConfig();
    return strtolower($cfg->getNested('def.database_provider', 'sqlite'));
  }

  public function validate(): bool {
    $type = $this->getProvider();
    $logger = $this->plugin->getLogger();
    try {
      if (isset($this->providers['database'][$type])) {
        $provider = $this->providers['database'][$type];
        if ($provider instanceof DatabaseProvider) {
          $this->open()->createTable();
          $logger->notice('Database provider selected: ' . $provider->name);
          return true;
        } else {
          throw new \InvalidArgumentException('Database error: Provider ' . $type . ' was not registered!');
        }
      } else {
        throw new \InvalidArgumentException('Invalid database provider: ' . $type);
      }
    } catch (\InvalidArgumentException $e) {
      return false;
    }
  }

  public function open(): DatabaseProvider {
    $type = $this->getProvider();
    if (isset($this->providers['database'][$type])) {
      return $this->providers['database'][$type];
    } else {
      throw new \InvalidArgumentException('Invalid database provider: ' . $type);
    }
  }
}