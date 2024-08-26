<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoPmqaCampanhaPontoColetaArquivo extends Model
{
    use HasFactory;

    protected $table   = 'exec_ponto_coleta_imagem';
    protected $guarded = ['id', 'created_at'];

    public function coleta()
    {
        return $this->belongsTo(related: ServicoPmqaCampanhaPontoColeta::class, foreignKey: 'fk_ponto_coleta');
    }
}
