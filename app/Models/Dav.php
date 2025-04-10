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
    'contrato_id',
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
    'status',
    'aereo_valor',
    'terrestre_tipo',
    'terrestre_valor',
    'aquatico_valor'
  ];

  protected $casts = [
    'origem'         => 'array',
    'destino'        => 'array',
    'profissionais'  => 'array',
    'transporte'     => 'array',
    'terrestre_tipo' => 'array'
  ];
}
