<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AtFaunaExecucaoCampanha extends Model
{
    use SoftDeletes;

    protected $table = 'at_fauna_execucao_campanhas';

    protected $guarded = ['id'];

    public function recurso_responsaveis()
    {
        return $this->hasMany(AtFaunaExecucaoCampanhaResposavel::class, 'at_fauna_execucao_campanha_id');
    }
}
