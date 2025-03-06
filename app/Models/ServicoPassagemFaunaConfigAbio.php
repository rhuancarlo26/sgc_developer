<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServicoPassagemFaunaConfigAbio extends Model
{
    use HasFactory;

    protected $table = 'passagem_fauna_config_abio';
    protected $guarded = ['id'];

    public function servico(): BelongsTo
    {
        return $this->belongsTo(Servicos::class, 'id_servico');
    }

    public function licenca()
    {
        return $this->belongsTo(Licenca::class, 'id_licenca');
    }

    public function abio_ret()
    {
        return $this->hasOne(ServicoPassagemFaunaConfigAbioRet::class, 'id_abio');
    }
}
