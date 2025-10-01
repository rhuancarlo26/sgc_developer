<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SgcEspeleoCampanha extends Model
{
    use HasFactory;

    protected $table = 'sgc_espeleo_campanhas';

    protected $fillable = [
        'id_contrato',
        'id_campanha',
        'status',
        'versao_analise',
        'cod_emp',
        'subproduto',
        'subtrecho',
        'segmento',
        'extensao',
        'tipo_de_intervencao',
        'descricao',
        'bioma',
    ];

    protected $casts = [
        'versao_analise' => 'integer',
        'extensao' => 'decimal:2',
    ];

    public function contrato()
    {
        return $this->belongsTo(Contrato::class, 'id_contrato');
    }

    public function empreendimento()
    {
        return $this->belongsTo(SgcvwEmpreendimentos::class, 'cod_emp', 'cod_emp');
    }

    public function justificativas()
    {
        return $this->hasMany(SgcEspeleoJustificativa::class, 'campanha_id');
    }

    public function metodologia()
    {
        return $this->hasOne(SgcEspeleoMetodologia::class, 'campanha_id');
    }
}