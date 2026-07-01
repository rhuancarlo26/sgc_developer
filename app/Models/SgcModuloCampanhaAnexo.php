<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SgcModuloCampanhaAnexo extends Model
{
    protected $table = 'sgc_modulo_campanha_anexos';

    protected $fillable = [
        'campanha_id',
        'nome_arquivo',
        'caminho_arquivo',
    ];

    public function campanha()
    {
        return $this->belongsTo(SgcModuloCampanha::class, 'campanha_id');
    }
}
