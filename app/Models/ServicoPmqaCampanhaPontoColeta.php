<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoPmqaCampanhaPontoColeta extends Model
{
    use HasFactory;

    protected $table = 'servico_pqma_campanha_ponto_coletas';
    protected $guarded = ['id', 'created_at'];
    protected $casts = [
        'sem_coleta' => 'bool'
    ];
}