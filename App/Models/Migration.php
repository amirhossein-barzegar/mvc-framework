<?php 

namespace App\Models;

use Config\DB;
use Exception;

class Migration extends DB {
  
  public function migrateQuery($query) {
    $db = new DB;
    try {
      mysqli_query($db->connection,$query);
    } catch(Exception $e) {
      echo $e;
    }
  }

  public function createUsersTable()
  {
    $this->migrateQuery('
      CREATE TABLE IF NOT EXISTS users (
        id INT NOT NULL AUTO_INCREMENT,
        name VARCHAR(64),
        email VARCHAR(255) UNIQUE,
        password TEXT,
        PRIMARY KEY (id)
      )
    ');
  }

  public function createPostsTable() {
    $this->migrateQuery('
      CREATE TABLE IF NOT EXISTS posts (
        id INT NOT NULL AUTO_INCREMENT,
        title VARCHAR(255),
        description TEXT,
        user_id INT,
        created_at DATETIME default CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        FOREIGN KEY (user_id) REFERENCES users(id)
      )
    ');
  }

  // public function insertIntoUsers(){
  //   $this->migrateQuery('
  //     INSERT INTO users (name, email, password) VALUES ("mostafa","mostafa@gmail.com","fdasfsdf")
  //   ');
  // }
}



function databaseMigrate() {
  $migration = new Migration;
  $methods = get_class_methods($migration);
  foreach($methods as $method):
      if ($method != 'migrateQuery' && $method != '__construct') {
          $migration->{$method}();
      }
  endforeach;
}

databaseMigrate();





