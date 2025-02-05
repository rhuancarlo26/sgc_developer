<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dav extends Model
{
    use HasFactory;

    protected $table = 'sgc_dav';

    protected $guarded = ['id', 'created_at'];

    protected $fillable = [
        'contrato', 
        'empreendimento', 
        'coordenador', 
        'finalidade', 
        'escopo', 
        'dataInicio', 
        'dataFinal', 
        'produto', 
        'subproduto', 
        'origem', 
        'destino', 
        'profissionais', 
        'transporte',
        'status'
    ];

    protected $casts = [
        'origem' => 'array',
        'destino' => 'array',
        'profissionais' => 'array',
        'transporte' => 'array',
    ];
}
