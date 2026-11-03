<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SgcLicencaCondicionante extends Model
{
    protected $table = 'sgc_licencas_condicionantes';

    protected $fillable = [
        'licenca_id', 'numero', 'grupo', 'titulo', 'descricao', 'tipo_prazo', 'prazo_dias', 'data_fixa',
        'dia_recorrencia', 'mes_recorrencia', 'relatorio_sigla', 'periodo_ano_anterior', 'situacao',
        'situacao_descricao', 'ordem',
    ];

    protected $casts = [
        'data_fixa' => 'date:Y-m-d',
        'prazo_dias' => 'integer',
        'dia_recorrencia' => 'integer',
        'mes_recorrencia' => 'integer',
        'periodo_ano_anterior' => 'boolean',
        'ordem' => 'integer',
    ];

    public function licenca(): BelongsTo
    {
        return $this->belongsTo(SgcLicenca::class, 'licenca_id');
    }

    public function prazos(): HasMany
    {
        return $this->hasMany(SgcLicencaPrazo::class, 'condicionante_id');
    }

    /**
     * Rótulo curto usado na descrição dos prazos (ex.: "Cond. 2.11 - RARA").
     */
    public function rotulo(): string
    {
        $complemento = $this->relatorio_sigla ?: $this->titulo;

        return 'Cond. ' . $this->numero . ($complemento ? " - {$complemento}" : '');
    }
}
