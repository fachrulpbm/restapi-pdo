<?php

class Buku {

    private $connection;
    private $tableName = "buku";

    public $id;
    public $judul;
    public $pengarang;
    public $tahunTerbit;
    public $stok;

    public function __construct($database) {
        $this->connection = $database;
    }    

    public function read(): PDOStatement {
        $query = "SELECT * FROM {$this->tableName} ORDER BY id ASC";
        $statement = $this->connection->prepare($query);
        $statement->execute();
        return $statement;
    }

    public function readById(): void {
        $query = "SELECT * FROM {$this->tableName} WHERE id = ?";
        $statement = $this->connection->prepare($query);

        $statement->bindParam(1, $this->id);
        $statement->execute();

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        $this->judul = $row["judul"];
        $this->pengarang = $row["pengarang"];
        $this->tahunTerbit = $row["tahun_terbit"];
        $this->stok = $row["stok"];
    }

    public function create() {
        $query = "INSERT INTO {$this->tableName} SET judul=:judul, pengarang=:pengarang, tahun_terbit=:tahun_terbit, stok=:stok";

        $statement = $this->connection->prepare($query);

        $statement->bindParam(":judul", $this->judul);
        $statement->bindParam(":pengarang", $this->pengarang);
        $statement->bindParam(":tahun_terbit", $this->tahunTerbit);
        $statement->bindParam(":stok", $this->stok);

        if ($statement->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function update() {
        $query = "UPDATE {$this->tableName} SET judul=:judul, pengarang=:pengarang, tahun_terbit=:tahun_terbit, stok=:stok WHERE id=:id";

        $statement = $this->connection->prepare($query);

        $statement->bindParam(":judul", $this->judul);
        $statement->bindParam(":pengarang", $this->pengarang);
        $statement->bindParam(":tahun_terbit", $this->tahunTerbit);
        $statement->bindParam(":stok", $this->stok);
        $statement->bindParam(":id", $this->id);

        if ($statement->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function delete() {
        $query = "DELETE FROM {$this->tableName} WHERE id = ?";
        $statement = $this->connection->prepare($query);

        $statement->bindParam(1, $this->id);
        
        if ($statement->execute()) {
            return true;
        } else {
            return false;
        }
    }
}
