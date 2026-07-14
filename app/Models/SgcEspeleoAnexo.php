<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SgcEspeleoAnexo extends Model
{
    protected $table = 'sgc_espeleo_anexo';

    protected $fillable = [
        'id_contrato',
        'campanha_id',
        'tipo',
        'caminho',
        'nome',
        'legenda',
        'size',
    ];

    public function contrato()
    {
        return $this->belongsTo(Contrato::class, 'id_contrato');
    }

    public function campanha()
    {
        return $this->belongsTo(SgcEspeleoCampanha::class, 'campanha_id');
    }
}
