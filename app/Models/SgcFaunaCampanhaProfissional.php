<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SgcFaunaCampanhaProfissional extends Model
{
    protected $table = 'sgc_fauna_campanha_profissionais';
    protected $guarded = ['id', 'created_at'];

    public function campanha()
    {
        return $this->belongsTo(SgcFaunaCampanha::class, 'campanha_id');
    }

    public function profissional()
    {
        return $this->belongsTo(SgcFaunaProfissionais::class, 'profissional_id');
    }
}