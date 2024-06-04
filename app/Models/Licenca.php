<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Licenca extends Model
{
    use HasFactory;

    protected $table = 'licenca';

    protected $guarded = ['id', 'created_at'];

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(LicencaTipo::class, 'tipo_id');
    }

    public function condicionantes(): HasMany
    {
        return $this->hasMany(LicencaCondicionante::class, 'licenca_id');
    }

    public function segmentos(): HasMany
    {
        return $this->hasMany(LicencaSegmento::class, 'licenca_id');
    }

    public function documento(): HasOne
    {
        return $this->hasOne(LicencaDocumento::class, 'licenca_id');
    }

    public function shapefile(): HasOne
    {
        return $this->hasOne(LicencaShapefile::class, 'licenca_id');
    }

    public function requerimentos(): HasMany
    {
        return $this->hasMany(LicencaRequerimento::class, 'licenca_id');
    }
}
