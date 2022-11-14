<?php

namespace ImperaZim\EasyGroups\Utils\form;

use pocketmine\player\Player;
use pocketmine\form\Form as IForm;

abstract class Form implements IForm {

 private $callable;
 protected $data = [];

 public function __construct(?callable $callable) {
  $this->callable = $callable;
 }

 public function sendToPlayer(Player $player) : void {
  $player->sendForm($this);
 }

 public function getCallable() : ?callable {
  return $this->callable;
 }

 public function setCallable(?callable $callable) {
  $this->callable = $callable;
 }

 public function handleResponse($player, $data) : void {
  $this->processData($data);
  $callable = $this->getCallable();
  if ($callable !== null) {
   $callable($player, $data);
  }
 }

 public function processData(&$data) : void {}

 public function jsonSerialize() {
  return $this->data;
 }
} 
