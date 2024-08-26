<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlanoSupressao extends Model
{
    use SoftDeletes;

    protected $table = 'plano_supressao';

    protected $guarded = ['id', 'created_at'];

    public function arquivo(): BelongsTo
    {
        return $this->belongsTo(Arquivo::class);
    }

}
