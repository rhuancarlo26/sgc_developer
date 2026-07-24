<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SgcRimaAnexo extends Model
{
    protected $table = 'sgc_rima_anexos';

    protected $fillable = [
        'sgc_rima_id',
        'nome_arquivo',
        'caminho_arquivo',
    ];

    public function rima()
    {
        return $this->belongsTo(SgcRima::class, 'sgc_rima_id');
    }
}
