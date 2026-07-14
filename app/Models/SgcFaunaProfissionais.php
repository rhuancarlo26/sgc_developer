<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SgcFaunaProfissionais extends Model
{
    protected $table = 'sgc_fauna_profissionais';
    protected $guarded = ['id', 'created_at'];

    public function campanhas()
    {
        return $this->hasMany(SgcFaunaCampanhaProfissional::class, 'profissional_id', 'id');
    }

}