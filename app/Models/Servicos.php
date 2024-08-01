<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Servicos extends Model
{
    use HasFactory;

    protected $table = 'servicos';
    protected $guarded = ['id', 'created_at'];

//    public function status(): BelongsTo
//    {
//        return $this->belongsTo(ServicoStatus::class, 'status_aprovacao');
//    }

    public function tema(): BelongsTo
    {
        return $this->belongsTo(ServicoTema::class, 'tema_servico');
    }

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(ServicoTipo::class, 'servico');
    }

    public function rhs(): HasManyThrough
    {
        return $this->hasManyThrough(RecursoRh::class, ServicoRh::class, 'id_servico', 'id', 'id', 'id_rh');
    }

    public function veiculos(): HasManyThrough
    {
        return $this->hasManyThrough(RecursoVeiculo::class, ServicoVeiculo::class, 'id_servico', 'id', 'id', 'id_veiculo');
    }

    public function equipamentos(): HasManyThrough
    {
        return $this->hasManyThrough(RecursoEquipamento::class, ServicoEquipamento::class, 'id_servico', 'id', 'id', 'id_equipamento');
    }

    public function condicionantes(): HasManyThrough
    {
        return $this->hasManyThrough(LicencaCondicionante::class, ServicoLicencaCondicionante::class, 'id_servico', 'id', 'id', 'id_condicionante');
    }
}
