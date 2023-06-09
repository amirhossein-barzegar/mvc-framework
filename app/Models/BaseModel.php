<?php

namespace App\Models;

use config\DB;
use AllowDynamicProperties;
use App\Responses\Response;

#[AllowDynamicProperties]
class BaseModel extends DB  implements \JsonSerializable
{
    public function jsonSerialize(): array
    {
        $methods = get_class_methods(static::class);
        $model = [];
        foreach($methods as $method) {
            if (str_starts_with($method, 'get')) {
                $property = pascalToSnake(substr($method,3, strlen($method)));
                $model[$property] = static::$method();
            }
        }
        return $model;
    }
    protected static string $table;
    protected static string $primaryKey = 'id';

    public static function create(array $data): static|false|Response
    {
        $fields = implode(',', array_keys($data));
        $values = ':' . implode(', :', array_keys($data));
        $query = "INSERT INTO " . static::$table . " ({$fields}) VALUES ({$values})";
        $stmt = self::$pdo->prepare($query);
        foreach ($data as $key=>$value) {
            $stmt->bindValue($key, $value);
        }
        try {
            $stmt->execute();
        } catch(\PDOException $e) {
            $response = new Response();
            $response->setHeader('Content-Type', 'application/json');
            $response->setBody([
                'state' => 'error',
                'error_code' => 3,
                'message' => $e->getMessage()
            ]);
            return $response;
        }
        return static::findById(self::$pdo->lastInsertId());
    }

    public static function update(int $id,array $data): static|false|Response
    {
        $updated = self::updateBy(static::$primaryKey, $id, $data);
        if ($updated instanceof Response) {
            return $updated;
        } elseif($updated) {
            return static::findById($id);
        }
        return false;
    }

    public static function updateBy(string $field, mixed $value,array $data): bool|Response
    {
        $fields = implode(', ', array_map(fn($data) => $data.' = :'.$data,array_keys($data)));
        $stmt = self::$pdo->prepare("UPDATE " . static::$table . " SET $fields WHERE $field = :$field");

        $stmt->bindValue($field, $value);

        foreach ($data as $key=>$val) {
            $stmt->bindValue($key, $val);
        }
        try {
            $stmt->execute();
        } catch(\PDOException $e) {
            $response = new Response();
            $response->setHeader('Content-Type', 'application/json');
            $response->setBody([
                'state' => 'error',
                'error_code' => 3,
                'message' => $e->getMessage()
            ]);
            return $response;
        }
        return $stmt->rowCount();
    }

    public static function delete(int $id): static|false|Response
    {
        $model = static::findById($id);
        $stmt = self::$pdo->prepare("DELETE FROM " . static::$table . " WHERE ".static::$primaryKey." = :".static::$primaryKey);
        $stmt->bindValue(static::$primaryKey,$id);
        try {
            $stmt->execute();
        } catch(\PDOException $e) {
            $response = new Response();
            $response->setHeader('Content-Type', 'application/json');
            $response->setBody([
                'state' => 'error',
                'error_code' => 3,
                'message' => $e->getMessage()
            ]);
            return $response;
        }
        if ($stmt->rowCount()) {
            return $model;
        } else {
            return false;
        }
    }

    public static function all(string $relation = ''): array|Response
    {
        if (is_string($relation) && strlen($relation) > 0) {
            $relationRes = (new static)->$relation();
            if (is_array($relationRes['fields'])) {
                $relateFields = implode(',', array_map(fn($field) => $relationRes['table'].'.'.$field,$relationRes['fields']));
            } else {
                $relateFields = $relationRes['table'].'*'.$relationRes['fields'];
            }
            $query = "SELECT customer_passwords.*,$relateFields FROM ".static::$table." INNER JOIN ".$relationRes['table']." ON ".$relationRes['foreign']."=".$relationRes['reference'];
            $stmt = self::$pdo->prepare($query);
            try {
                $stmt->execute();
            } catch(\PDOException $e) {
                $response = new Response();
                $response->setHeader('Content-Type', 'application/json');
                $response->setBody([
                    'state' => 'error',
                    'error_code' => 3,
                    'message' => $e->getMessage()
                ]);
                return $response;
            }
            $records = $stmt->fetchAll();
            $models = [];
            foreach($records as $record) {
                $model = new static();
                $relatedModel = new $relationRes['model'];
                if ($record) {
                    foreach($record as $field=>$value) {
                        $setter = propSetterName($field);
                        if (method_exists($model,$setter)) {
                            $model->$setter($value);
                        } else {
                            $relatedModel->$setter($value);
                        }
                    }
                    $model->$relation = $relatedModel;
                    $models[] = $model;
                }
            }
            return $models;
        } else {
            $query = "SELECT * FROM ".static::$table;
            $stmt = self::$pdo->prepare($query);
            try {
                $stmt->execute();
            } catch(\PDOException $e) {
                $response = new Response();
                $response->setHeader('Content-Type', 'application/json');
                $response->setBody([
                    'state' => 'error',
                    'error_code' => 3,
                    'message' => $e->getMessage()
                ]);
                return $response;
            }
            $records = $stmt->fetchAll();
            $models = [];
            foreach($records as $record) {
                $model = new static();
                if (is_iterable($record)) {
                    foreach($record as $field=>$value) {
                        $setter = propSetterName($field);
                        if (method_exists($model,$setter)) {
                            $model->$setter($value);
                        }
                    }
                    $models[] = $model;
                }
            }
            return $models;
        }

    }

    public static function findById(mixed $value, array|string $relations = ''): static|false|Response
    {
        return self::findBy(static::$primaryKey, $value, $relations);
    }


    public static function findBy(string $field, mixed $value, array|string $relations = ''): static|false|Response
    {
        if (is_iterable($relations)) {
            $queries = '';
            $relatedFields = '';
            foreach($relations as $relation) {
                $relationRes = (new static)->$relation();
                if (isset($relationRes['fields']) && is_iterable($relationRes['fields'])) {
                    $relatedFields .= implode(', ',array_merge($relationRes['fields'],[$relationRes['reference'],$relationRes['foreign']]));
                } elseif (isset($relationRes['fields']) && $relationRes['fields'] == '*') {
                    $relatedFields .= $relationRes['table'].'.'.$relationRes['fields'];
                } else {
                    $relatedFields .= $relationRes['table'].'.*';
                }
                $queries .= "LEFT JOIN ".$relationRes['table']." ON ".$relationRes['reference']." = ".$relationRes['foreign'];
            }
            $query = "SELECT ".static::$table.".*, ".$relatedFields." FROM ".static::$table." ".$queries." WHERE $field = :$field";
            $stmt = self::$pdo->prepare($query);
            $stmt->bindParam($field, $value);
            try {
                $stmt->execute();
            } catch(\PDOException $e) {
                $response = new Response();
                $response->setHeader('Content-Type', 'application/json');
                $response->setBody([
                    'state' => 'error',
                    'error_code' => 3,
                    'message' => $e->getMessage()
                ]);
                return $response;
            }
            $model = new static();
            foreach ($relations as $relation) {
                $relationRes = (new static)->$relation();
//                dumper(self::handleRelations($stmt, $relationRes,$model, $relation));die;
                return self::handleRelations($stmt, $relationRes,$model, $relation);
            }
        } elseif(is_string($relations) && strlen($relations) > 0) {
            $relationRes = (new static)->$relations();
            $relatedFields = ', ';
            if (is_iterable($relationRes['fields'])) {
                $relatedFields .= implode(', ',array_merge($relationRes['fields'],[$relationRes['reference'],$relationRes['foreign']]));
            } elseif ($relationRes['fields'] == '*') {
                $relatedFields .= $relationRes['table'].'.'.$relationRes['fields'];
            } else {
                $relatedFields .= $relationRes['table'].'.*';
            }
//            dumper($relatedFields);die;
            $join = " INNER JOIN ".$relationRes['table']." ON ".$relationRes['reference']." = ".$relationRes['foreign'];
            $query = "SELECT ".static::$table.".*".$relatedFields." FROM ".static::$table."$join WHERE $field = :$field";
            $stmt = self::$pdo->prepare($query);
            $stmt->bindParam($field, $value);
            try {
                $stmt->execute();
            } catch(\PDOException $e) {
                $response = new Response();
                $response->setHeader('Content-Type', 'application/json');
                $response->setBody([
                    'state' => 'error',
                    'error_code' => 3,
                    'message' => $e->getMessage()
                ]);
                return $response;
            }
            $model = new static();
            return self::handleRelations($stmt,$relationRes,$model,$relations);
        } else {
            $query = "SELECT * FROM ".static::$table." WHERE $field = :$field";
            $stmt = self::$pdo->prepare($query);
            $stmt->bindParam($field, $value);
            try {
                $stmt->execute();
            } catch(\PDOException $e) {
                $response = new Response();
                $response->setHeader('Content-Type', 'application/json');
                $response->setBody([
                    'state' => 'error',
                    'error_code' => 3,
                    'message' => $e->getMessage()
                ]);
                return $response;
            }
            $record = $stmt->fetch();
            $model = new static();
            if (is_iterable($record)) {
                foreach($record as $field=>$value) {
                    $setter = propSetterName($field);
                    if (method_exists($model, $setter)) {
                        $model->$setter($value);
                    }
                }
                return $model;
            } else {
                return false;
            }
        }
        return false;
    }


    private static function handleRelations($stmt, $relationRes,$model,$relation): static|false|Response
    {
        switch ($relationRes['relation']) {
            case 'many':
                $records = $stmt->fetchAll();
                $relatedModels = [];
                foreach($records as $record) {
                    if (is_iterable($record)) {
                        $relatedModel = new $relationRes['model'];
                        foreach($record as $field=>$value) {
                            $setter = propSetterName($field);
                            if (method_exists($model, $setter)) {
                                $model->$setter($value);
                            } elseif (method_exists($relatedModel,$setter)) {
                                if ($value) {
                                    $relatedModel->$setter($value);
                                } else {
                                    $response = new Response;
                                    $response->setHeader('Content-Type', 'application/json');
                                    $response->setBody([
                                        'state' => 'error',
                                        'error_code' => 5,
                                        'message' => 'No records found'
                                    ]);
                                    return $response;
                                }
                            }
                        }
                        $relatedModels[] = $relatedModel;
                    } else {
                        return false;
                    }
                    $model->$relation = $relatedModels;
                }
                if (!isset($model->$relation)) {
                    $model->$relation = null;
                }
                return $model;
                break;
            case 'one':
                $record = $stmt->fetch();
                $relatedModel = new $relationRes['model'];
                if (is_iterable($record)) {
                    foreach($record as $field=>$value) {
                        $setter = propSetterName($field);
                        if (method_exists($model, $setter)) {
                            $model->$setter($value);
                        } elseif (method_exists($relatedModel,$setter)) {
                            if ($value) {
                                $relatedModel->$setter($value);
                            } else {
                                $response = new Response;
                                $response->setHeader('Content-Type', 'application/json');
                                $response->setBody([
                                    'state' => 'error',
                                    'error_code' => 5,
                                    'message' => 'No record found'
                                ]);
                                return $response;
                            }
                        }
                    }
                    $model->$relation = $relatedModel;
                    return $model;
                } else {
                    return false;
                }
                break;
        }
        return false;
    }

    public function attach(array $data, string $pivot_table): static|bool|Response
    {
        $fields = implode(', ', array_keys($data));
        $values = ':'.implode(', :', array_keys($data));
        $stmt = self::$pdo->prepare("INSERT INTO ".$pivot_table." ($fields) VALUES ($values)");

        foreach($data as $key=>$value) {
            $stmt->bindValue($key, $value);
        }
        try {
            $stmt->execute();
        } catch(\PDOException $e) {
            $response = new Response();
            $response->setHeader('Content-Type', 'application/json');
            $response->setBody([
                'state' => 'error',
                'error_code' => 3,
                'message' => $e->getMessage()
            ]);
            return $response;
        }
        return (bool)$stmt->rowCount();
    }
}
