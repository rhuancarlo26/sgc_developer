<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SgcPmqa extends Model
{
    protected $table = 'sgc_pmqa';

    protected $guarded = ['id'];

    public function contrato(): BelongsTo
    {
        return $this->belongsTo(related: Contrato::class, foreignKey: 'id_contrato');
    }

        public function getRouteKeyName(): string
    {
        return 'id'; // explícito (ou troque se não for id)
    }

        /** 📌 Tema PMQA */
    public function tema(): BelongsTo
    {
        return $this->belongsTo(ServicoTema::class, 'tema');
    }

    /** 📊 Resultados */
    public function resultados(): HasMany
    {
        return $this->hasMany(SgcPmqaResultado::class, 'campanha_id');
    }

    /** 📍 Pontos */
    public function pontos(): HasMany
    {
        return $this->hasMany(SgcPmqaPonto::class, 'campanha_id');
    }

    /** 📅 Campanhas */
    public function campanhas(): HasMany
    {
        return $this->hasMany(SgcPmqaCampanha::class, 'campanha_id');
    }

    // /** 🧪 Parâmetros */
    // public function parametros(): HasMany
    // {
    //     return $this->hasMany(SgcPmqaListaPivot::class, 'campanha_id');
    // }

    /** 📝 Configuração de parecer */
    // public function configuracaoParecer(): HasOne
    // {
    //     return $this->hasOne(ServicoPmqaConfiguracaoParecer::class, 'fk_servico');
    // }

    // /** 🧾 Parecer PMQA */
    // public function parecer(): HasOne
    // {
    //     return $this->hasOne(ServicoParecerPMQAConfiguracao::class, 'fk_servico');
    // }
}
