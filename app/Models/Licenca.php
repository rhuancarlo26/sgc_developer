<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Licenca extends Model
{
    use HasFactory;

    protected $table = 'licenca';

    protected $guarded = ['id', 'created_at'];

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(LicencaTipo::class, 'tipo_id', 'id');
    }

    public function condicionantes(): HasMany
    {
        return $this->hasMany(LicencaCondicionante::class, 'licenca_id');
    }

    public function requerimentos(): HasMany
    {
        return $this->hasMany(LicencaRequerimento::class, 'licenca_id');
    }
}