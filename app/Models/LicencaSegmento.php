<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LicencaSegmento extends Model
{
    use HasFactory;

    protected $table = 'licencas_br';
    protected $primaryKey = 'idlicenca_br';
    protected $guarded = ['idlicenca_br', 'created_at'];

    public function uf_inicial_rel(): BelongsTo
    {
        return $this->belongsTo(Uf::class, 'uf_inicial');
    }

    public function uf_final_rel(): BelongsTo
    {
        return $this->belongsTo(Uf::class, 'uf_final');
    }
}
