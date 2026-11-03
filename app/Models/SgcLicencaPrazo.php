<?php

namespace App\Models;

use App\Domain\Sgc\Licencas\Enums\SituacaoPrazo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SgcLicencaPrazo extends Model
{
    public const ORIGEM_RENOVACAO = 'renovacao';
    public const ORIGEM_CONDICIONANTE = 'condicionante';
    public const ORIGEM_EVENTO = 'evento';

    protected $table = 'sgc_licencas_prazos';

    protected $fillable = [
        'licenca_id', 'condicionante_id', 'origem', 'referencia', 'descricao', 'ano_referencia', 'periodo_inicio',
        'periodo_fim', 'data_evento', 'data_limite_original', 'data_limite', 'status', 'data_cumprimento',
    ];

    protected $casts = [
        'periodo_inicio' => 'date:Y-m-d',
        'periodo_fim' => 'date:Y-m-d',
        'data_evento' => 'date:Y-m-d',
        'data_limite_original' => 'date:Y-m-d',
        'data_limite' => 'date:Y-m-d',
        'data_cumprimento' => 'date:Y-m-d',
        'ano_referencia' => 'integer',
    ];

    public function licenca(): BelongsTo
    {
        return $this->belongsTo(SgcLicenca::class, 'licenca_id');
    }

    public function condicionante(): BelongsTo
    {
        return $this->belongsTo(SgcLicencaCondicionante::class, 'condicionante_id');
    }

    public function registros(): HasMany
    {
        return $this->hasMany(SgcLicencaPrazoRegistro::class, 'prazo_id')->orderByDesc('data')->orderByDesc('id');
    }

    public function situacao(): SituacaoPrazo
    {
        if ($this->status === 'dispensado') {
            return SituacaoPrazo::Dispensado;
        }
        if ($this->status === 'cumprido') {
            return $this->data_cumprimento && $this->data_cumprimento->gt($this->data_limite)
                ? SituacaoPrazo::CumpridoComAtraso
                : SituacaoPrazo::Cumprido;
        }

        $hoje = now()->startOfDay();

        if ($this->data_limite->lt($hoje)) {
            return SituacaoPrazo::Vencido;
        }

        return $this->data_limite->lte($hoje->copy()->addDays(config('sgc_licencas.janela_a_vencer_dias')))
            ? SituacaoPrazo::AVencer
            : SituacaoPrazo::NoPrazo;
    }

    public function foiProrrogado(): bool
    {
        return !$this->data_limite->equalTo($this->data_limite_original);
    }

    /**
     * Dias até a data-limite (negativo quando vencido).
     */
    public function diasRestantes(): int
    {
        return (int) now()->startOfDay()->diffInDays($this->data_limite, false);
    }
}
