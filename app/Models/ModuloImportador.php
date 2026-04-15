<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModuloImportador extends Model
{
    use HasFactory;

    protected $table = 'modulo_importadores';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function dados(): HasMany
    {
        return $this->hasMany(ModuloImportadorDados::class, 'modulo_importador_id');
    }
}
