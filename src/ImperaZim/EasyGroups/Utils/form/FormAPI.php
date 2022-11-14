<?php

namespace ImperaZim\EasyGroups\Utils\form;

use ImperaZim\EasyGroups\Utils\form\types\ModalForm;
use ImperaZim\EasyGroups\Utils\form\types\CustomForm;

class FormAPI {
 
 public static function createCustomForm(?callable $function = null) : CustomForm {
  return new CustomForm($function);
 }
 
 public static function createModalForm(?callable $function = null) : ModalForm {
  return new ModalForm($function);
 }

}
