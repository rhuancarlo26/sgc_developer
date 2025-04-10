<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServicoPmqaCampanhaPontoColeta extends Model
{
    use HasFactory;

    protected $table = 'exec_ponto_coleta';
    protected $guarded = ['id', 'created_at'];
    protected $casts = ['coleta' => 'bool'];

    public function arquivos(): HasMany
    {
        return $this->hasMany(related: ServicoPmqaCampanhaPontoColetaArquivo::class, foreignKey: 'fk_ponto_coleta');
    }
}
