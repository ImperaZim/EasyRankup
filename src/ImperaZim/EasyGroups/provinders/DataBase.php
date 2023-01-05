<?php

namespace ImperaZim\EasyGroups\provinders;

class DataBase extends \ImperaZim\EasyGroups\EasyGroups {
 
 const TYPES = ["SQLITE3", "MYSQL", "YAML", "YML", "JSON"];
 
 public static function getDefault() : String {
  return self::getInstance()->getConfig()->getNested("group.default");
 }
 
 public static function getType() : String {
  return strtoupper(self::getInstance()->getConfig()->getNested('database.type'));
 }
 
 public static function checkType() : bool {
  if (in_array(self::getType(), self::TYPES)) {
   self::open()->createTable();
   self::getInstance()->getLogger()->notice("Database selected as: " . self::getType() . " TYPE");
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
   case "SQLITE3": case "SQLITE":
    return new Provinder\SQLite3();
   case "YAML": case "YML":
    return new Provinder\YAML();
   case "JSON":
    return new Provinder\JSON();
  }
 }
 
} 
