<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SgcPmqaCampanhaPontoColeta extends Model
{
    use HasFactory;

    protected $table = 'sgc_pmqa_ponto_coleta';
    protected $guarded = ['id', 'created_at'];
    protected $casts = ['coleta' => 'bool'];

    public function arquivos(): HasMany
    {
        return $this->hasMany(related: SgcPmqaCampanhaPontoColetaArquivo::class, foreignKey: 'pmqa_ponto_coleta_id');
    }
}
