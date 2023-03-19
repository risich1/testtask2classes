<?php

namespace App;

use PDO;

class DB implements ISource {

    protected PDO $pdo;
    protected string $table;
    protected string $query = '';
    protected array $params = [];

    public function __construct($host, $db, $user, $password) {

        $dsn = "mysql:host=$host;dbname=$db;charset=utf8";
        $opt = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $this->pdo = new PDO($dsn, $user, $password, $opt);
    }

    public function setTable(string $table): self {
        $this->table = $table;
        return $this;
    }

    public function where(string $column, string $operator, mixed $value): self {
        $this->params[] = $value;
        $this->query .= " WHERE $column $operator ?";

        return $this;
    }

    public function whereIn(string $column, array $values): self {
        $this->params = [...$this->params, ...$values];
        $this->query .= " WHERE $column IN (" . implode(',', str_split(str_repeat('?' , count($values)))) . ")";
        return $this;
    }

    public function andWhere($column, $operator, $value): self {
        $this->params[] = $value;
        $this->query .= " AND $column $operator ?";
        return $this;
    }

    public function orWhere($column, $operator, $value): self {
        $this->params[] = $value;
        $this->query .= " OR $column $operator ?";
        return $this;
    }

    public function get(array $fields = ["*"]): array {
        $fields = implode(',', $fields);
        $stmt = $this->pdo->prepare("SELECT $fields FROM $this->table" . $this->query);
        $stmt->execute($this->params);
        $this->resetState();
        return $stmt->fetchAll();
    }

    public function create(array $data) {
        $fields = implode(',', array_keys($data));
        $preparedData = implode(',', str_split(str_repeat('?' , count($data))));
        $stmt = $this->pdo->prepare("INSERT  $this->table(" . $fields  . ") VALUES (" . $preparedData . ")" );
        $stmt->execute(array_values($data));
        $this->resetState();
    }

    public function update(array $data) {

    }

    public function delete() {
        $stmt = $this->pdo->prepare("DELETE FROM $this->table" . $this->query);
        $stmt->execute($this->params);
        $this->resetState();
    }

    protected function resetState() {
        $this->params = [];
        $this->query = '';
    }
}
