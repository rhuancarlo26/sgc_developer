<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SgcFaunaModuloAmostral extends Model
{
    use SoftDeletes;

    protected $table = 'sgc_fauna_modulos_amostrais';
    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];
    protected $dates = ['deleted_at'];

    public function campanha()
    {
        return $this->belongsTo(SgcFaunaCampanha::class, 'campanha_id', 'id');
    }
}