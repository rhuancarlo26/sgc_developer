<?php

namespace App\Shared\Traits;

use Illuminate\Database\Eloquent\Model;

trait Deletable
{
    /**
     * Delete the Model from the database
     *
     * @param Model $model
     * @return bool
     */
    public function delete(Model $model): bool
    {
        return $model->delete();
    }

}
