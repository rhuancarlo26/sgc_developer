<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServicoPmqaCampanhaPontoMedicao extends Model
{
    use HasFactory;

    protected $table = 'exec_ponto_medicao';
    protected $guarded = ['id', 'created_at'];
    protected $casts = ['medido' => 'bool'];

    public function parametros()
    {
        return $this->HasMany(related: ServicoPmqaCampanhaPontoMedicaoParametro::class, foreignKey: 'fk_ponto_medicao');
    }

    public function arquivos()
    {
        return $this->hasMany(related: ServicoPmqaCampanhaPontoMedicaoArquivo::class, foreignKey: 'fk_ponto_medicao');
    }
}
