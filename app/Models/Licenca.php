<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Licenca extends Model
{
    use HasFactory;

    protected $table = 'licenca';

    protected $guarded = ['id', 'created_at'];

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(LicencaTipo::class, 'licenca_tipo_id', 'id');
    }
}
