<?php

namespace imperazim\easyrankup;

use pocketmine\utils\Config;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\event\player\PlayerJoinEvent;

use CortexPE\Commando\PacketHooker;
use imperazim\easyrankup\rank\RankFactory;
use imperazim\easyrankup\rank\RankManager;
use imperazim\easyrankup\provider\economy\types\EconomyProvider;
use imperazim\easyrankup\provider\economy\EconomyProviderManager;
use imperazim\easyrankup\provider\database\types\DatabaseProvider;
use imperazim\easyrankup\provider\database\DatabaseProviderManager;

final class Loader extends PluginBase implements Listener {

  public array $ranks;
  public array $providers;
  public ?EconomyProvider $economy;
  public ?DatabaseProvider $database;

  public function onLoad() : void {
    $this->saveResource('ranks.yml');
    $this->saveResource('commands.yml');
  }

  public function onEnable() : void {
    if (($epm = new EconomyProviderManager($this))->validate()) {
      $this->economy = $epm->open();
    }
    if (($dpm = new DatabaseProviderManager($this))->validate()) {
      $this->database = $dpm->open();
    }

    if ($this->economy instanceof EconomyProvider && $this->database instanceof DaseProvider) {
      if (!PacketHooker::isRegistered()) {
        PacketHooker::register($this);
      }
      if (array_count_values(($ranks = RankManager::getAll())) > 0) {
        RankFactory::registerAll($ranks);
      }
      $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }
  }
  
  public function onJoin(PlayerJoinEvent $event) : void {
    $this->getDatabaseProvider()->create($player->getName());
  }

  public function getEconomyProvider() : ?EconomyProvider {
    return $this->economy;
  }

  public function getDatabaseProvider() : ?DatabaseProvider {
    return $this->database;
  }

}