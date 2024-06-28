<?php

namespace ProductData;

class Database
{
    private $host = 'localhost';
    private $db = 'productdb';
    private $user = 'root';
    private $pass = 'OAK_ROOT6497';
    private $charset = 'utf8mb4';
    private $pdo;
    private $error;

    public function __construct()
    {
        $dsn = "mysql:host=$this->host;dbname=$this->db;charset=$this->charset";
        $options = [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new \PDO($dsn, $this->user, $this->pass, $options);
        } catch (\PDOException $e) {
            $this->error = $e->getMessage();
        }
    }

    public function getConnection()
    {
        return $this->pdo;
    }
}
