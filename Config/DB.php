<?php 

namespace Config;

use PDOException;
use PDO;

class DB {
  private static string $username;
  private static string $password;
  private static string $server;
  private static string $dbname;
  private static string $charset;
  public static object $connection;
  private static array $options;

  public static function connection(): PDO
  {
    self::$username = 'root';
    self::$password = 'root';
    self::$server = 'localhost';
    self::$dbname = 'phpapidb';
    self::$charset = 'utf8';
    self::$options = [
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ];
    try {
      static::$connection = new PDO(
          "mysql:host=".self::$server.";dbname=".self::$dbname.";charset=".self::$charset,
          self::$username,
          self::$password,
          self::$options
      );
    } catch(PDOException $e) {
      echo 'Connection was failed: ' . $e->getMessage() . ' in file '. $e->getFile() . ' On line '. $e->getLine();
    }
    return self::$connection;
  }
}