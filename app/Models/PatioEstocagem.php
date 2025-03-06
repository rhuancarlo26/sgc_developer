<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatioEstocagem extends Model
{
    use SoftDeletes;

    protected $table = 'patio_estocagem';

    protected $guarded = ['id', 'created_at'];

    public function fotos(): BelongsToMany
    {
        return $this->belongsToMany(Arquivo::class, 'arquivo_patio_estocagem', 'patio_estocagem_id', 'arquivo_id');
    }

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(TipoPatio::class, 'tipo_patio_id');
    }

    public function licenca(): BelongsTo
    {
        return $this->belongsTo(Licenca::class, 'licenca_id');
    }
}
