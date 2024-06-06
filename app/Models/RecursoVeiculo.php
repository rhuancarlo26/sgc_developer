<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RecursoVeiculo extends Model
{
    use HasFactory;

    protected $table = 'recurso_veiculos';
    protected $guarded = ['id', 'created_at'];

    public function codigo(): BelongsTo
    {
        return $this->belongsTo(RecursoVeiculoCodigo::class, 'veiculo_codigo_id');
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(RecursoVeiculoDocumento::class, 'recurso_veiculo_id');
    }
}
