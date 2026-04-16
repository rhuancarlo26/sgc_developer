<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModuloImportador extends Model
{
    use HasFactory;

    protected $table = 'modulo_importadores';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'load' => 'bool',
        'desc_erros' => 'array'
    ];

    public function modulo(): BelongsTo
    {
        return $this->belongsTo(Modulo::class, 'modulo_id');
    }

    public function dados(): HasMany
    {
        return $this->hasMany(ModuloImportadorDados::class, 'modulo_importador_id');
    }

    public function fotos(): HasMany
    {
        return $this->hasMany(ModuloImportadorFotos::class, 'modulo_importador_id');
    }

    public function anexos(): HasMany
    {
        return $this->hasMany(ModuloImportadorAnexos::class, 'modulo_importador_id');
    }
}
