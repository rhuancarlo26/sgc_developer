<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SgcFaunaQuelonios extends Model
{
    protected $table = 'sgc_fauna_amostragem_quelo_crocod';
    protected $guarded = ['id'];
    public $timestamps = true;

    protected $fillable = [
        'id_contrato',
        'id_campanha',
        'ponto_de_coleta',
        'nome_curso_hidrico',
        'coordenadas',
        'bacia_hidrografica',
        'profundidade',
        'largura',
        'tipo_substrato',
    ];

    public function campanha()
    {
        return $this->belongsTo(SgcFaunaCampanha::class, 'id_campanha', 'id');
    }
}