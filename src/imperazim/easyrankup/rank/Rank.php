<?php

namespace imperazim\easyrankup\rank;

final class Rank {
  
  private array $data;
  
  public function __construct(private string $name) {
    $this->data = RankFactory::getData($name);
  }
  
  public function getName() : string {
    return $this->name;
  }
  
  public function getDisplayTag() : string {
    return $this->data['tag'] ?? '';
  }
  
  public function getCost() : string {
    return $this->data['tag'] ?? 0;
  }
  
  public function getPermissions() : array {
    return $this->data['permission'] ?? [];
  }
  
  public function getKit() : array {
    return $this->data['kit'] ?? [];
  }
  
}