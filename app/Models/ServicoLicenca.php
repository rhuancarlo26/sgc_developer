<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServicoLicenca extends Model
{

    protected $table = 'servico_licenca';

    protected $guarded = ['id', 'created_at'];

    public function licenca(): BelongsTo
    {
        return $this->belongsTo(Licenca::class);
    }
}
