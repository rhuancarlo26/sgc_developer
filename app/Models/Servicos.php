<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Servicos extends Model
{
    use HasFactory;

    protected $table = 'servicos';
    protected $guarded = ['id', 'created_at'];

    public function status(): BelongsTo
    {
        return $this->belongsTo(ServicoStatus::class, 'servico_status_id');
    }

    public function tema(): BelongsTo
    {
        return $this->belongsTo(ServicoTema::class, 'servico_tema_id');
    }

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(ServicoTipo::class, 'servico_tipo_id');
    }

    public function rhs(): HasManyThrough
    {
        return $this->hasManyThrough(RecursoRh::class, ServicoRh::class, 'servico_id', 'id', 'id', 'recurso_rh_id');
    }

    public function veiculos(): HasManyThrough
    {
        return $this->hasManyThrough(RecursoVeiculo::class, ServicoVeiculo::class, 'servico_id', 'id', 'id', 'recurso_veiculo_id');
    }

    public function equipamentos(): HasManyThrough
    {
        return $this->hasManyThrough(RecursoEquipamento::class, ServicoEquipamento::class, 'servico_id', 'id', 'id', 'recurso_equipamento_id');
    }

    public function condicionantes(): HasManyThrough
    {
        return $this->hasManyThrough(LicencaCondicionante::class, ServicoLicencaCondicionante::class, 'servico_id', 'id', 'id', 'condicionante_id');
    }

    public function licencas_condicionantes()
    {
        return $this->hasMany(ServicoLicencaCondicionante::class, 'servico_id');
    }

    public function pontos()
    {
        return $this->hasMany(ServicoPmqaPonto::class, 'servico_id');
    }

    public function pmqa_config_lista_parecer()
    {
        return $this->hasOne(ServicoPmqaConfiguracaoParecer::class, 'servico_id');
    }
}
