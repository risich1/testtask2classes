<?php

namespace App;

use Exception;

abstract class ModelManager {

    protected Model $model;
    protected array $ids = [];
    protected array $models = [];

    /**
     * @throws Exception
     */
    public function __construct(array $conditions, Model $model)
    {
        $this->model = $model;
        $this->ids = $this->model->searchIds($conditions);

        if (!$this->ids) {
            throw new Exception('Ids are not found');
        }
    }

    /**
     * @throws Exception
     */
    public function loadModels(): array {
        $this->models = $this->model->loadByIds($this->ids);
        return $this->models;
    }

    /**
     * @throws Exception
     */
    public function deleteModel(Model $model): void {
        if (in_array($model->id, $this->ids)) {
            $model->delete();
        }
    }

}
