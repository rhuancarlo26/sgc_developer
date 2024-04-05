<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Servicos extends Model
{
    use HasFactory;

    protected $table = 'servicos';
    protected $guarded = ['id', 'created_at'];

    public function status(): BelongsTo
    {
        return $this->belongsTo(ServicoStatus::class, 'servico_status_id');
    }

    public function tema(): BelongsTo
    {
        return $this->belongsTo(ServicoTema::class, 'servico_tema_id');
    }

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(ServicoTipo::class, 'servico_tipo_id');
    }
}