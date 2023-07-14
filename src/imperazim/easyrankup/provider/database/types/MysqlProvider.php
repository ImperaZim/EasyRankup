<?php

namespace imperazim\easyrankup\provider\database\types;

use imperazim\easyrankup\Loader;

final class MysqlProvider implements DatabaseProvider {
  private \mysqli $database;
  private string $tableName = "profile";
  
  public string $database = "mysql";

  public function __construct(private Loader $plugin) {
    if ($plugin->database instanceof DatabaseProvider) {
      $config = $plugin->getConfig()->get("database-provider", []);
      $this->database = new \mysqli(
        $config["host"] ?? "0.0.0.0",
        $config["db"] ?? "your_db",
        $config["user"] ?? "root",
        $config["password"] ?? "admin",
        $config["port"] ?? 3306
      );
    }
  }

  public function createTable(): void {
    $query = "CREATE TABLE IF NOT EXISTS $this->tableName (name TEXT, rankid INT)";
    $this->database->query($query);
  }

  public function exists(string $player): bool {
    $stmt = $this->database->prepare("SELECT * FROM $this->tableName WHERE name=?");
    $stmt->bind_param("s", $player);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
  }

  public function create(string $player): bool {
    if ($this->exists($player)) {
      return false;
    }

    $stmt = $this->database->prepare("INSERT INTO $this->tableName (name, rankid) VALUES (?, ?)");
    $stmt->bind_param("si", $player, 0);
    $stmt->execute();
    return true;
  }

  public function getValue(string $player, string $key): mixed {
    if (!$this->exists($player)) {
      return 0;
    }

    $stmt = $this->database->prepare("SELECT $key FROM $this->tableName WHERE name=?");
    $stmt->bind_param("s", $player);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_array();
    $value = $row[0] ?? 0;
    $result->free_result();
    return $value;
  }

  public function addValue(string $player, string $key, mixed $value): bool|int {
    if (!$this->exists($player)) {
      return false;
    }

    $stmt = $this->database->prepare("UPDATE $this->tableName SET $key=? WHERE name=?");
    $stmt->bind_param("is", $value, $player);
    $stmt->execute();
    return true;
  }

}