<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServicoMonitoraFaunaConfigAbio extends Model
{
    use HasFactory;

    protected $table = 'fauna_config_abio';
    protected $guarded = ['id'];

    public function licenca(): BelongsTo
    {
        return $this->belongsTo(Licenca::class, 'id_licenca');
    }

    public function rets(): HasMany
    {
        return $this->hasMany(ServicoMonitoraFaunaConfigAbioRet::class, 'id_abio');
    }
}
