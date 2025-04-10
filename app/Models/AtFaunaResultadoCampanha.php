<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AtFaunaResultadoCampanha extends Model
{
    use SoftDeletes;

    protected $table = 'at_fauna_resultado_campanha';

    protected $guarded = ['id'];

    public function campanha()
    {
        return $this->belongsTo(AtFaunaExecucaoCampanha::class, 'fk_campanha');
    }
}
