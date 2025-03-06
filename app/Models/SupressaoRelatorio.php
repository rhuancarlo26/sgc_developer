<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupressaoRelatorio extends Model
{
    use SoftDeletes;

    protected $table = 'supressao_relatorio';

    protected $guarded = ['id'];

    public function anexos(): HasMany
    {
        return $this->hasMany(SupressaoRelatorioAnexo::class, 'fk_relatorio');
    }

    public function servico(): BelongsTo
    {
        return $this->belongsTo(Servicos::class, 'fk_servico');
    }
}
