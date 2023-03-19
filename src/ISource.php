<?php

namespace App;

interface ISource {
    public function setTable(string $table): self;
    public function where(string $column, string $operator, mixed $value): self;
    public function get(array $fields): array;
    public function create(array $data);
    public function update(array $data);
    public function delete();
}
