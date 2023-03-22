<?php 

namespace Config;

use Exception;

class DB {
  protected string $username;
  protected string $password;
  protected string $server;
  protected string $dbname;
  protected object $connection;

  protected $sql;

  public function __construct()
  {
    $this->username = 'root';
    $this->password = '';
    $this->server = '127.0.0.1';
    $this->dbname = 'tessst';
    try {
      $this->connection = mysqli_connect(
        $this->server,
        $this->username,
        $this->password,
        $this->dbname
      );
    } catch(Exception $e) {
      echo $e->getMessage();
    }
  }
}