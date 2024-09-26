<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Servicos extends Model
{
    use HasFactory;

    protected $table = 'servicos';
    protected $guarded = ['id', 'created_at'];

//    public function status(): BelongsTo
//    {
//        return $this->belongsTo(ServicoStatus::class, 'status_aprovacao');
//    }

    public function contrato(): BelongsTo
    {
        return $this->belongsTo(related: Contrato::class, foreignKey: 'id_contrato');
    }

    public function tema(): BelongsTo
    {
        return $this->belongsTo(related: ServicoTema::class, foreignKey: 'tema_servico');
    }

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(related: ServicoTipo::class, foreignKey: 'tipo_servico');
    }

    public function rhs(): HasManyThrough
    {
        return $this->hasManyThrough(
            related: RecursoRh::class,
            through: ServicoRh::class,
            firstKey: 'id_servico',
            secondKey: 'id',
            localKey: 'id',
            secondLocalKey: 'id_rh'
        );
    }

    public function veiculos(): HasManyThrough
    {
        return $this->hasManyThrough(
            related: RecursoVeiculo::class,
            through: ServicoVeiculo::class,
            firstKey: 'id_servico',
            secondKey: 'id',
            localKey: 'id',
            secondLocalKey: 'id_veiculo'
        );
    }

    public function equipamentos(): HasManyThrough
    {
        return $this->hasManyThrough(
            related: RecursoEquipamento::class,
            through: ServicoEquipamento::class,
            firstKey: 'id_servico',
            secondKey: 'id',
            localKey: 'id', secondLocalKey: 'id_equipamento'
        );
    }

    public function condicionantes(): HasManyThrough
    {
        return $this->hasManyThrough(
            related: LicencaCondicionante::class,
            through: ServicoLicencaCondicionante::class,
            firstKey: 'id_servico',
            secondKey: 'id',
            localKey: 'id',
            secondLocalKey: 'id_condicionante'
        );
    }

    public function parecerSupressaoVegetacao(): HasOne
    {
        return $this->hasOne(related: ServicoParecerSupressaoConfiguracao::class, foreignKey: 'fk_servico');
    }

    public function licencas_condicionantes()
    {
        return $this->hasMany(ServicoLicencaCondicionante::class, 'id_servico');
    }

    public function pontos(): HasMany
    {
        return $this->hasMany(ServicoPmqaPonto::class, 'fk_servico');
    }

    public function parametros(): HasMany
    {
        return $this->hasMany(ServicoPmqaParametroLista::class, 'fk_servico');
    }

    public function pmqa_config_lista_parecer(): HasOne
    {
        return $this->hasOne(ServicoPmqaConfiguracaoParecer::class, 'fk_servico');
    }

    public function cont_ocorr_parecer_configuracao(): HasOne
    {
        return $this->hasOne(ServicoContOcorrSupervisaoParecerConfiguracao::class, 'fk_servico');
    }

    public function parecer(): HasOne
    {
        return $this->hasOne(related: ServicoParecer::class, foreignKey: 'fk_servico');
    }

    public function parecerPmqa(): HasOne
    {
        return $this->hasOne(related: ServicoParecerPMQAConfiguracao::class, foreignKey: 'fk_servico');
    }

    public function parecerAfugentamento(): HasOne
    {
        return $this->hasOne(related: ServicoParecerAfugentamentoConfiguracao::class, foreignKey: 'fk_servico');
    }

    public function parecerOcorrencia(): HasOne
    {
        return $this->hasOne(related: ServicoContOcorrSupervisaoParecerConfiguracao::class, foreignKey: 'fk_servico');
    }

    public function supervisao_lotes()
    {
        return $this->hasMany(ServicoContOcorrSupervisaoConfigLote::class, 'id_servico');
    }

    public function passagem_fauna_abios()
    {
        return $this->hasMany(ServicoPassagemFaunaConfigAbio::class, 'id_servico');
    }

    public function passagem_fauna_passagens()
    {
        return $this->hasMany(ServicoPassagemFaunaConfigPassagem::class, 'id_servico');
    }

    public function parecer_passagem_fauna()
    {
        return $this->hasOne(ServicoPassagemFaunaParecerConfiguracao::class, 'fk_servico');
    }

}
