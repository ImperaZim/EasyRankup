<?php

namespace imperazim\easyrankup\provider\database\types;

use imperazim\easyrankup\Loader;

final class SqliteProvider implements DatabaseProvider {
  private \SQLite3 $database;
  private string $tableName = "profile";
  
  public string $database = "sqlite";

  public function __construct(private Loader $plugin) {
    $databasePath = $plugin->getDataFolder() . 'database.sqlite';
    $this->database = new \SQLite3($databasePath);
  }

  public function createTable(): void {
    $query = "CREATE TABLE IF NOT EXISTS $this->tableName (name TEXT, rankid INT)";
    $this->database->exec($query);
  }

  public function exists(string $player): bool {
    $stmt = $this->database->prepare("SELECT * FROM $this->tableName WHERE name=:name");
    $stmt->bindValue(":name", $player, SQLITE3_TEXT);
    $result = $stmt->execute();
    $row = $result->fetchArray();
    return $row !== null;
  }

  public function create(string $player): bool {
    if ($this->exists($player)) {
      return false;
    }

    $stmt = $this->database->prepare("INSERT INTO $this->tableName (name, rankid) VALUES (:name, :rankid)");
    $stmt->bindValue(":name", $player, SQLITE3_TEXT);
    $stmt->bindValue(":rankid", 0, SQLITE3_INTEGER);
    $stmt->execute();
    return true;
  }

  public function getValue(string $player, string $key): mixed {
    if (!$this->exists($player)) {
      return 0;
    }

    $stmt = $this->database->prepare("SELECT $key FROM $this->tableName WHERE name=:name");
    $stmt->bindValue(":name", $player, SQLITE3_TEXT);
    $result = $stmt->execute();
    $row = $result->fetchArray();
    $value = $row[$key] ?? null;
    return $value;
  }

  public function addValue(string $player, string $key, mixed $value): bool|int {
    if (!$this->exists($player)) {
      return false;
    }

    $stmt = $this->database->prepare("UPDATE $this->tableName SET $key=:value WHERE name=:name");
    $stmt->bindValue(":value", $value, SQLITE3_INTEGER);
    $stmt->bindValue(":name", $player, SQLITE3_TEXT);
    $stmt->execute();
    return true;
  }
}