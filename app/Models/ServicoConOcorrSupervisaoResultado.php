<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServicoConOcorrSupervisaoResultado extends Model
{
    use HasFactory;

    protected $table = 'supervisao_resultado';
    protected $guarded = ['id'];

    public function analise()
    {
        return $this->hasOne(ServicoConOcorrSupervisaoResultadoAnalise::class, 'id_resultado');
    }

    public function outras_analises(): HasMany
    {
        return $this->hasMany(ServicoConOcorrSupervisaoResultadoOutrasAnalises::class, 'id_resultado');
    }
}
