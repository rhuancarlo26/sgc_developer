<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoPmqaCampanhaPontoMedicaoArquivo extends Model
{
    use HasFactory;

    protected $table   = 'exec_ponto_medicao_laudo';
    protected $guarded = ['id', 'created_at'];

    public function medicao()
    {
        return $this->belongsTo(related: ServicoPmqaCampanhaPontoMedicao::class, foreignKey: 'fk_ponto_medicao');
    }
}
