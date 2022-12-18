<?php

namespace ImperaZim\EasyRankup\Functions\DataBase;

class DataBase extends \ImperaZim\EasyRankup\EasyRankup {
 
 public static function getType() : String {
  return strtoupper(self::getInstance()->getConfig()->get('database-type'));
 }
 
 public static function checkType() : bool {
  if (in_array(strtoupper(self::gettype()), ["SQLITE3", "MYSQL"])) {
   self::getInstance()->getLogger()->notice("Database loaded successfully: " . self::getType() . " type");
   self::open()->createTable();
   return true;
  }else{
   self::getInstance()->getLogger()->warning("Database loading error: Database type does not exist!");
   return false;
  }
 }
 
 public static function open() {
  switch (self::getType()) {
   case "MYSQL":
    return new Provinder\MySQL();
   case "SQLITE3":
    return new Provinder\SQLite3();
  }
 }
 
}
