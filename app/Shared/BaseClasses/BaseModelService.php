<?php

namespace App\Shared\BaseClasses;

use Exception;
use Illuminate\Database\Eloquent\Model;

abstract class BaseModelService
{
    protected readonly Model $model;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        if (!isset($this->modelClass)) {
            throw new Exception('Propriedade $modelClass não definida');
        }

        $this->model = new $this->modelClass;

    }
}
