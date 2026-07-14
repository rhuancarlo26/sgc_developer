<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SgcFaunaResultadosConsideracoes extends Model
{
    protected $table = 'sgc_fauna_resultados_consideracoes';
    protected $guarded = ['id'];
    public $timestamps = true;

    public function campanha()
    {
        return $this->belongsTo(SgcFaunaCampanha::class, 'id_campanha');
    }
}