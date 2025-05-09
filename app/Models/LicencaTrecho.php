<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LicencaTrecho extends Model
{
    use HasFactory;

    protected $table = 'licenca_trechos';

    protected $guarded = ['id', 'created_at'];

    public function uf(): BelongsTo
    {
        return $this->belongsTo(Uf::class, 'uf_id');
    }

    public function rodovia(): BelongsTo
    {
        return $this->belongsTo(Rodovia::class, 'rodovia_id');
    }
}
