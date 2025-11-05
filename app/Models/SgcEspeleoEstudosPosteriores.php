<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SgcEspeleoEstudosPosteriores extends Model
{
    use HasFactory;

    protected $table = 'sgc_espeleo_estudos_posteriores';

    protected $fillable = [
        'id_contrato',
        'campanha_id',
        'necessario',
        'subproduto_id',
        'quantidade',
        'coordenadas',
    ];

    protected $casts = [
        'necessario' => 'boolean',
        'coordenadas' => 'array',
    ];

    public function campanha()
    {
        return $this->belongsTo(SgcEspeleoCampanha::class, 'campanha_id');
    }

    public function subproduto()
    {
        return $this->belongsTo(SgcvwSubprodutos::class, 'subproduto_id');
    }
}
