<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SgcMalarigeno extends Model
{
    protected $table = 'sgc_malarigeno';

    protected $fillable = [
        'id_contrato',
        'subproduto',
        'modulo_id',
        'status',
        'planilha_nome',
        'planilha_caminho',
    ];

    protected $casts = [
        'id_contrato' => 'integer',
        'modulo_id' => 'integer',
    ];

    public function fotos()
    {
        return $this->hasMany(SgcMalarigenoFoto::class, 'sgc_malarigeno_id');
    }

    public function anexos()
    {
        return $this->hasMany(SgcMalarigenoAnexo::class, 'sgc_malarigeno_id');
    }
}
