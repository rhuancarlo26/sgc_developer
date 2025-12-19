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
    'seq_dav',
    'empreendimento',
    'coordenador',
    'dataInicio',
    'dataFinal',
    'produto',
    'subproduto',
    'profissionais',
    'transporte',
    'status',
    'aereo_valor',
    'terrestre_tipo',
    'terrestre_valor',
    'aquatico_valor',
    'origem_sei'
  ];

  protected $casts = [
    'profissionais'  => 'array',
    'transporte'     => 'array',
    'terrestre_tipo' => 'array'
  ];
}
