<?php

namespace ImperaZim\EasyGroups\forms; 

use ImperaZim\EasyGroups\forms\types\ListForm;
use ImperaZim\EasyGroups\forms\types\CustomForm;
use ImperaZim\EasyGroups\forms\types\QuestionForm;

class FormAPI {

 public static function sendListForm(?callable $function = null) : ListForm {
  return new ListForm($function);
 }
 
 public static function sendCustomForm(?callable $function = null) : CustomForm {
  return new CustomForm($function);
 }

 public static function sendQuestionForm(?callable $function = null) : QuestionForm {
  return new QuestionForm($function);
 }

} 
