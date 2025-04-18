<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AreaSupressao extends Model
{
    use SoftDeletes;

    protected $table = 'area_supressao';

    protected $guarded = ['id', 'created_at'];

    public function corteEspecies(): HasMany
    {
        return $this->hasMany(CorteEspecie::class, 'area_supressao_id');
    }

    public function fotos(): BelongsToMany
    {
        return $this->belongsToMany(Arquivo::class, 'arquivo_area_supressao', 'area_supressao_id', 'arquivo_id');
    }

    public function licenca(): BelongsTo
    {
        return $this->belongsTo(Licenca::class, 'licenca_id');
    }

    public function bioma(): BelongsTo
    {
        return $this->belongsTo(TipoBioma::class, 'tipo_bioma_id');
    }

    public function estagioSucessional(): BelongsTo
    {
        return $this->belongsTo(EstagioSucessional::class, 'estagio_sucessional_id');
    }
}
