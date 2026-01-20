<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SgcPmqaPonto extends Model
{
    protected $table = 'sgc_pmqa_pontos';

    protected $guarded = ['id', 'created_at'];

    /** 🔗 PMQA (raiz do domínio) */
    public function pmqa(): BelongsTo
    {
        return $this->belongsTo(SgcPmqa::class, 'campanha_id');
    }
}
