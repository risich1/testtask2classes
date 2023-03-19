<?php

namespace App;

use Exception;

abstract class Model {

    protected array $fields = [];
    protected array $data  = [];
    protected string $table;
    protected ISource $source;

    /**
     * @throws Exception
     */
    public function __construct(int $id = 0, array $data = [])
    {
        $this->source = SourceFabric::getSource('db')->setTable($this->table);

        if ($id) {
            $model = $this->source->where('id', '=', $id)->get();
            if (!$model) {
                throw new Exception("Search by id was failed in " . static::class);
            }
            $this->data = array_shift($model);
        }

        if ($data) {
            $this->data = $this->validatedData($data);
        }
    }

    /**
     * @throws Exception
     */
    public function __get(string $name)
    {
        $this->checkIssetField($name);
        return $this->data[$name];
    }

    /**
     * @throws Exception
     */
    public function __set(string $name, $value): void
    {
        $this->checkIssetField($name);
        $this->validatedData([$name => $value]);
        $this->data[$name] = $value;
    }

    /**
     * @throws Exception
     */
    protected function checkIssetField(string $name): void {
        if (!in_array($name, array_keys($this->data))) {
            throw new Exception("Field $name is not available in " . static::class);
        }
    }

    /**
     * @throws Exception
     */
    public function save(): void {
        if (isset($this->data['id'])) {
           $this->source->where('id', '=', $this->data['id'])->update($this->data);
        } else {
          $this->source->create($this->data);
        }
    }

    /**
     * @throws Exception
     */
    public function delete(): void {
        if (!isset($this->data['id'])) {
            throw new Exception("Id is required in " . static::class);
        }

        $this->source->where('id', '=', $this->data['id'])->delete();
    }

    public function searchIds(array $conditions): array {
        foreach ($conditions as $index => $condition) {
            if ($index === 0) {
                $this->source->where(...$condition);
            } else {
                $this->source->andWhere(...$condition);
            }
        }
        return array_column($this->source->get(['id']), 'id');
    }

    /**
     * @throws Exception
     */
    public function loadByIds(array $ids): array {
        $result = $this->source->whereIn('id', $ids)->get();
        foreach ($result as &$item) {
            $item = new static(data: $item);
        }

        return $result;
    }

    public function transform(): object {
        return (object) $this->data;
    }

    /**
     * @throws Exception
     */
    protected function validatedData(array $data): array {
        $fieldsForCompare = $this->fields;
        $dataForCompare = $data;
        unset($fieldsForCompare['id'], $dataForCompare['id']);
        foreach ($dataForCompare as $field => $value) {
            if (!isset($this->fields[$field])) {
                unset($dataForCompare[$field], $data[$field]); continue;
            }
            if (!FieldsValidator::validate($value, $this->fields[$field])) {
                throw new Exception("Invalid field '$field' value. It must be " . $this->fields[$field]);
            }
        }
        if (count($dataForCompare) < count($fieldsForCompare)) {
            throw new Exception("All fields are required in " . static::class);
        }

        return $data;
    }

}
