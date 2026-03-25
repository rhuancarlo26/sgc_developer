<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServicoContOcorrSupervisaoConfigLote extends Model
{
    use HasFactory;

    protected $table = 'supervisao_config_lotes';
    protected $guarded = ['id'];

    public function rodovia(): BelongsTo
    {
        return $this->belongsTo(Rodovia::class, 'id_rodovia');
    }

    public function uf()
    {
        return $this->belongsTo(Uf::class, 'id_uf');
    }
}