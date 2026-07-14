<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AtFaunaRelatorio extends Model
{
    use SoftDeletes;

    protected $table = 'at_fauna_relatorio';

    protected $guarded = ['id'];

    public function servico(): BelongsTo
    {
        return $this->belongsTo(Servicos::class, 'fk_servico');
    }
}
