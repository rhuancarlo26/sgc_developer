<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SgcCronogramaAuxiliar extends Model
{
    use HasFactory;

    protected $table = 'sgc_cronograma_aux';
    protected $guarded = ['id', 'created_at']; 
    public $timestamps = false; 

    protected $fillable = [
        'contrato_id',
        'cod_emp',
        'contrato',
        'item_edital',
        'subproduto',
        'data_de_inicio_previsto',
        'data_de_entrega_previsto',
    ];
}