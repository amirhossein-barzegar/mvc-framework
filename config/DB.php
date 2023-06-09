<?php 

namespace Config;

use PDOException;
use PDO;

class DB {
    protected static PDO $pdo;
    private string $username = 'root';
    private string $password = 'root';
    private string $host = 'localhost';
    private string $dbname = 'test4k';
    private string $charset = 'utf8';
    private static self $instance;
    public static function instance(): void
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
    }

    protected function __construct()
    {
        $dsn = "mysql:host=$this->host;dbname=$this->dbname;charset=$this->charset";
        $options = [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];
        try {
            self::$pdo = new PDO($dsn, $this->username, $this->password, $options);
        } catch(PDOException $e) {
            echo 'Connection was failed: ' . $e->getMessage() . ' in file '. $e->getFile() . ' On line '. $e->getLine();
        }
    }

    protected static function run(string $query): void
    {
        self::$pdo->exec($query);
    }
}