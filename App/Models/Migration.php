<?php 

namespace App\Models;

use Config\DB;
use Exception;

class Migration extends DB {
  
public function migrateQuery(string $query): void
{
    try {
        DB::connection();
        DB::$connection->exec($query);
    } catch(Exception $e) {
        echo $e->getMessage();
    }
}

  public function createUsersTable()
  {
    $this->migrateQuery('
      CREATE TABLE IF NOT EXISTS users (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(64),
        email VARCHAR(255) UNIQUE,
        password TEXT
      )
    ');
  }

  public function createPostsTable() {
    $this->migrateQuery('
      CREATE TABLE IF NOT EXISTS posts (
        id INT PRIMARY KEY AUTO_INCREMENT,
        title VARCHAR(255),
        description TEXT,
        user_id INT REFERENCES users (id) ON DELETE CASCADE,
        created_at DATETIME default CURRENT_TIMESTAMP
      )
    ');
  }

//   public function insertIntoUsers()
//   {
//     $this->migrateQuery('
//       INSERT INTO users (name, email, password) VALUES
//            ("Mostafa","mostafa@gmail.com","fdasfsdf"),
//            ("Amirhossein", "amirhossein@gmail.com", "amir1383"),
//            ("Abolfazl", "abolfazl@gmail.com", "abol1234")
//     ');
//   }
//
//   public function insertIntoPosts()
//   {
//       $this->migrateQuery('
//            INSERT INTO posts (title, description, user_id) VALUES
//                ("Post 1 book", "dummy description for post 1", 2),
//                ("Post 2 book", "dummy description for post 2", 3),
//                ("Post 3 book", "dummy description for post 3", 2),
//                ("Post 4 book", "dummy description for post 3", 1),
//                ("Post 5 book", "dummy description for post 3", 3)
//       ');
//   }
}



function databaseMigrate(): void
{
  $migration = new Migration;
  $methods = get_class_methods($migration);
  foreach($methods as $method):
      if ($method != 'migrateQuery' && $method != '__construct') {
          $migration->{$method}();
      }
  endforeach;
}

databaseMigrate();





