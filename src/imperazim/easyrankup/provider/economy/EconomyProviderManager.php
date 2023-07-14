<?php

namespace imperazim\easyrankup\provider\economy;

use imperazim\easyrankup\Loader;
use imperazim\easyrankup\provider\economy\types\EconomyProvider;
use imperazim\easyrankup\provider\economy\types\EconomyApiProvider;
use imperazim\easyrankup\provider\economy\types\EasyEconomyProvider;
use imperazim\easyrankup\provider\economy\types\BedrockEconomyProvider;

final class EconomyProviderManager {
  private Loader $plugin;

  public function __construct(Loader $plugin) {
    $this->plugin = $plugin;
    $this->plugin->providers = [];
    $this->register('economyapi', new EconomyApiProvider($plugin));
    $this->register('easyeconomy', new EasyEconomyProvider($plugin));
    $this->register('bedrockeconomy', new BedrockEconomyProvider($plugin));
  }

  public function register(string $name, EconomyProvider $provider): void {
    $this->providers['economy'][strtolower($name)] = $provider;
  }

  public function getProvider(): string {
    $cfg = $this->plugin->getConfig();
    return strtolower($cfg->getNested('def.economy_provider', 'EconomyAPI'));
  }

  public function validate(): bool {
    $type = $this->getProvider();
    $logger = $this->plugin->getLogger();
    try {
      if (isset($this->providers['economy'][$type])) {
        $provider = $this->providers['economy'][$type];
        if ($provider instanceof EconomyProvider) {
          $logger->notice('Economy provider selected: ' . $provider->name);
          if ($provider->economy === null) {
            throw new \InvalidArgumentException('Plugin ' . $provider->name . ' is not installed on the server!');
          }
          return true;
        } else {
          throw new \InvalidArgumentException('Economy error: Provider ' . $type . ' was not registered!');
        }
      } else {
        throw new \InvalidArgumentException('Invalid economy provider: ' . $type);
      }
    } catch (\InvalidArgumentException $e) {
      return false;
    }
  }

  public function open(): EconomyProvider {
    $type = $this->getProvider();

    if (isset($this->providers['economy'][$type])) {
      return $this->providers['economy'][$type];
    } else {
      throw new \InvalidArgumentException('Invalid economy provider: ' . $type);
    }
  }
}