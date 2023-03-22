<?php 

namespace App\Traits;

trait ModelTrait {
  public string $table;

  public function __construct() {
    $this->setTable();
  }

  public function setTable() {
    $namespace = static::class;
    $array = explode('\\',$namespace);
    $class = strtolower(end($array));
    $tableName = $class.'s';
    return $this->table = $tableName;
  }
}