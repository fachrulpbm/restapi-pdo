<?php

class Database{

    private $host = "localhost";
    private $databaseName = "perpustakaan";
    private $userName = "root";
    private $password = "";
    public $connection;

    public function getConnection(){
        $this->connection = null;
        try {
            $this->connection = new PDO("mysql:host={$this->host};dbname={$this->databaseName}", $this->userName, $this->password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "Connection error: {$exception->getMessage()}";
        }
        return $this->connection;
    }

}