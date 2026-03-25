<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class AtFaunaResultadoCampanha extends Model
{


    protected $table = 'at_fauna_resultado_campanha';

    protected $guarded = ['id'];

    public function campanha()
    {
        return $this->belongsTo(AtFaunaExecucaoCampanha::class, 'fk_campanha');
    }
}
