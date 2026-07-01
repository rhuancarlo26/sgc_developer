<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SgcModuloCampanha extends Model
{
    protected $table = 'sgc_modulo_campanhas';

    protected $fillable = [
        'id_contrato',
        'produto',
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
        return $this->hasMany(SgcModuloCampanhaFoto::class, 'campanha_id');
    }

    public function anexos()
    {
        return $this->hasMany(SgcModuloCampanhaAnexo::class, 'campanha_id');
    }

    public function modulo()
    {
        return $this->belongsTo(SgcModulo::class, 'modulo_id');
    }
}
