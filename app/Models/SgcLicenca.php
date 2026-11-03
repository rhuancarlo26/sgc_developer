<?php

namespace App\Models;

use App\Domain\Sgc\Licencas\Enums\SituacaoLicenca;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class SgcLicenca extends Model
{
    use SoftDeletes;

    protected $table = 'sgc_licencas';

    protected $fillable = [
        'tipo_id', 'numero', 'orgao_emissor', 'uf_id', 'empreendimento', 'processo_sei', 'sei_documento',
        'data_emissao', 'data_validade', 'antecedencia_renovacao_dias', 'licenca_anterior_id', 'motivo_vinculo',
        'contrato_id', 'status', 'observacoes', 'arquivo_nome', 'arquivo_caminho', 'created_by',
    ];

    protected $casts = [
        'data_emissao' => 'date:Y-m-d',
        'data_validade' => 'date:Y-m-d',
        'antecedencia_renovacao_dias' => 'integer',
    ];

    protected $hidden = ['arquivo_caminho'];

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(SgcLicencaTipo::class, 'tipo_id');
    }

    public function uf(): BelongsTo
    {
        return $this->belongsTo(Uf::class, 'uf_id');
    }

    public function contrato(): BelongsTo
    {
        return $this->belongsTo(Contrato::class, 'contrato_id');
    }

    public function anterior(): BelongsTo
    {
        return $this->belongsTo(self::class, 'licenca_anterior_id');
    }

    public function sucessoras(): HasMany
    {
        return $this->hasMany(self::class, 'licenca_anterior_id');
    }

    public function condicionantes(): HasMany
    {
        return $this->hasMany(SgcLicencaCondicionante::class, 'licenca_id')->orderBy('ordem')->orderBy('id');
    }

    public function prazos(): HasMany
    {
        return $this->hasMany(SgcLicencaPrazo::class, 'licenca_id');
    }

    public function prazoRenovacao(): HasOne
    {
        return $this->hasOne(SgcLicencaPrazo::class, 'licenca_id')->where('origem', SgcLicencaPrazo::ORIGEM_RENOVACAO);
    }

    public function responsaveis(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'sgc_licencas_responsaveis', 'licenca_id', 'user_id')->withTimestamps();
    }

    public function scopeAtivas(Builder $query): Builder
    {
        return $query->where('status', 'ativa');
    }

    public function scopeRegularizacao(Builder $query, bool $regularizacao = true): Builder
    {
        return $query->whereHas('tipo', fn ($q) => $q->where('regularizacao', $regularizacao));
    }

    /**
     * Situação calculada. Requer a relação prazoRenovacao (com registros) para identificar a renovação.
     */
    public function situacao(): SituacaoLicenca
    {
        if ($this->status === 'substituida') {
            return SituacaoLicenca::Substituida;
        }
        if ($this->status === 'arquivada') {
            return SituacaoLicenca::Arquivada;
        }
        if (!$this->data_validade) {
            return SituacaoLicenca::Vigente;
        }

        $hoje = now()->startOfDay();
        $renovacao = $this->prazoRenovacao;
        $solicitada = $renovacao && $renovacao->status === 'cumprido';

        if ($this->data_validade->lt($hoje)) {
            // Renovação requerida dentro do prazo mantém a vigência até a manifestação do órgão (LC 140/2011, art. 14, § 4º)
            $tempestiva = $solicitada && $renovacao->data_cumprimento && $renovacao->data_cumprimento->lte($renovacao->data_limite);
            return $tempestiva ? SituacaoLicenca::VigenciaProrrogada : SituacaoLicenca::Vencida;
        }

        if ($solicitada) {
            return SituacaoLicenca::RenovacaoSolicitada;
        }

        $janela = config('sgc_licencas.janela_a_vencer_dias');
        if ($renovacao && $renovacao->data_limite->lte($hoje->copy()->addDays($janela))) {
            return SituacaoLicenca::AVencer;
        }

        return SituacaoLicenca::Vigente;
    }

    public function rotulo(): string
    {
        return trim(($this->tipo?->sigla ?? '') . ' nº ' . $this->numero);
    }
}
