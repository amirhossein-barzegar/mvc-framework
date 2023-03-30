<?php 

namespace App\Traits;

use App\Models\Employee;
use App\Models\Model;
use Exception;

trait ModelTrait {
  public static string $table;
  private static object $fetchObject;
  private static array $fetchArray;
  public array $relations;

  public function __construct() {
    $this->setTable();
  }

  public static function setTable(): string
  {
    $namespace = static::class;
    $array = explode('\\',$namespace);
    $class = strtolower(end($array));
    $tableName = $class.'s';
    return static::$table = $tableName;
  }
  
  protected static function getPrimaryKey(): string
  {
      $connection = static::connection();
      $stmt = $connection->prepare("SHOW KEYS FROM ".static::$table." WHERE Key_name = 'PRIMARY'");
      $stmt->execute();
      return $stmt->fetch()['Column_name'];
  }

    public static function all(array|string $relations = ''): array|self
    {
        $connection = static::connection();
        $stmt = $connection->prepare("SELECT * FROM ".static::$table);
        $stmt->execute();
//        $stmt->setFetchMode(\PDO::FETCH_CLASS, static::class);
        foreach ($stmt->fetchAll() as $rowKey=>$row) {
            self::$fetchArray[$rowKey] = new static;
            foreach($row as $key=>$rowItem) {
                // Check column name in hidden array? start
                if (property_exists(self::$fetchArray[$rowKey],'hidden') && in_array($key, self::$fetchArray[$rowKey]?->hidden))
                    continue;
                // Check column name in hidden array? end
                self::$fetchArray[$rowKey]->$key = $rowItem;
            }
        }
    
        // add relations
        if ($relations != '') {
            if(is_array($relations)) {
                foreach($relations as $relation) {
                    $relates = (new static)->$relation();
                }
            } else {
                $relates = (new static)->$relations();
            }
        }
        
        return self::$fetchArray;
    }
    
    /**
     * Find record of model table with id
     *
     * @param int $id
     * @param array|string $relations
     *
     * @return array|static
     */
    public static function findById(int $id, array|string $relations = ''): static|array
    {
        $connection = static::connection();
        $stmt = $connection->prepare("SELECT * FROM ".static::$table." WHERE id = :id");
        $process = $stmt->execute(['id' => $id]);
        if ($res = $stmt->fetch()) {
            self::$fetchObject = new static;
            foreach ($res as $rowKey=>$row) {
                // Check column name in hidden array? start
                if (property_exists(self::$fetchObject,'hidden') && in_array($rowKey, self::$fetchObject?->hidden))
                    continue;
                // Check column name in hidden array? end
                self::$fetchObject->$rowKey = $row;
            }
    
            // add relations
            if ($relations != '') {
                if(is_array($relations)) {
                    foreach($relations as $relation) {
                        (new static)->$relation();
                    }
                } else {
                    (new static)->$relations();
                }
            }
    
            return self::$fetchObject;
        } else {
            http_response_code(404);
            echo 'Page not found 404'.'<br>';
            return [];
        }
    }
    
    /**
     * Fetch array or object in Model format
     * @param Model|string $model
     * @param array|object $items
     *
     * @return array|object
     */
    public function fetchModel(Model|string $model,array|object $items): array|object
    {
        $result = null;
        $model = new $model;
        foreach ($items as $key=>$item) {
            // Check column name in hidden array? start
            if (property_exists($model,'hidden') && in_array($key, $model?->hidden))
                continue;
            // Check column name in hidden array? end
            $model->$key = $item;
        }
        return $model;
    }
    
    /**
     * Create new record for model table
     */
    public static function create(array $data) {
        $connection = static::connection();
        $questionMarkHolders = "";
        foreach(array_values($data) as $index=>$d)
        {
            if (count($data) > $index+1) $questionMarkHolders.= "?, ";
            else $questionMarkHolders.= "?";
        }
        $stmt = $connection->prepare("INSERT INTO " . static::$table . " (" . implode(', ',array_keys($data)) . ") VALUES ($questionMarkHolders)");
        return $stmt->execute(array_values($data));
    }
    
    public function delete()
    {
        $connection = static::connection();
        $stmt = $connection->prepare("DELETE FROM " . static::$table . " WHERE id = ?");
        return $stmt->execute([self::$fetchObject->id]);
    }
    
    public function edit(array $data): bool
    {
        $connection = static::connection();
        $stmt = $connection->prepare("UPDATE " . static::$table . " SET " . implode(' = ?, ',array_keys($data)) . " = ? WHERE id = " . self::$fetchObject->id);
        return $stmt->execute(array_values($data));
    }
    
    
    
    
    /******************************************************************
     * Relationships
     * ****************************************************************
     */
    
    /**
     * Belongs to many related records with foreign key
     * @param $className
     * @param $foreignKey
     *
     * @return void
     */
    public function belongsTo($className,$foreignKey): void
    {
        $connection = static::connection();
        $ownClass = new ($this::class);
        $ownTable = $ownClass::setTable();
        $relatedClass = new $className;
        $relatedPrimaryKey = $relatedClass::getPrimaryKey();
        $relatedTable = $relatedClass::setTable();
        $stmt = $connection->prepare("SELECT * FROM $relatedTable");
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_OBJ);
        $fetch = $stmt->fetch();
        foreach (self::$fetchArray as $rowKey=>$row) {
            if ($row->$foreignKey == $fetch->$relatedPrimaryKey) {
                $row->relations[$relatedTable] = $this->fetchModel($relatedClass,$fetch);
            }
        }
    }
    
    /**
     * Belongs to many related records with foreign key
     * @param $className
     * @param $foreignKey
     *
     * @return void
     */
    public function belongsToMany($className,$foreignKey): void
    {
        $connection = static::connection();
        $ownClass = new ($this::class);
        $ownTable = $ownClass::setTable();
        $relatedClass = new $className;
        $relatedPrimaryKey = $relatedClass::getPrimaryKey();
        $relatedTable = $relatedClass::setTable();
//        $stmt = $connection->prepare("SELECT * FROM ".$ownTable." RIGHT JOIN ".$relatedTable." ON ".$relatedTable.".$relatedPrimaryKey = ".$ownTable.".$foreignKey");
        $stmt = $connection->prepare("SELECT * FROM $relatedTable");
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_OBJ);
        $fetchAll = $stmt->fetchAll();
        if (isset(self::$fetchArray)) {
            foreach (self::$fetchArray as $rowKey=>$row) {
                foreach($fetchAll as $item) {
                    if ($row->$foreignKey === $item->$relatedPrimaryKey) {
                        $row->relations[$relatedTable][] = $this->fetchModel($relatedClass,$item);
                    }
                }
            }
        } else {
            foreach($fetchAll as $item) {
                if (self::$fetchObject->$foreignKey === $item->$relatedPrimaryKey) {
                    self::$fetchObject->relations[$relatedTable][] = $this->fetchModel($relatedClass,$item);
                }
            }
        }
    }
    
    /**
     * Has Many related records with foreign key
     * @param $className
     * @param $foreignKey
     *
     * @return void
     */
    public function hasMany($className,$foreignKey): void
    {
        $connection = static::connection();
        $ownClass = new ($this::class);
        $ownTable = $ownClass::setTable();
        $relatedClass = new $className;
        $ownPrimaryKey = $ownClass::getPrimaryKey();
        $relatedTable = $relatedClass::setTable();
        $stmt = $connection->prepare("SELECT * FROM $relatedTable");
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_OBJ);
        $fetchAll = $stmt->fetchAll();
        foreach (self::$fetchArray as $rowKey=>$row) {
            foreach($fetchAll as $item) {
                if ($row->$ownPrimaryKey == $item->$foreignKey) {
                    $row->relations[$relatedTable][] = $this->fetchModel($relatedClass,$item);
                }
            }
        }
    }
}