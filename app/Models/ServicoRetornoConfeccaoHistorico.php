<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServicoRetornoConfeccaoHistorico extends Model
{
    use HasFactory;

    protected $table = 'servico_retorno_confeccao_historicos';

    protected $fillable = [
        'servico_id',
        'contrato_id',
        'tema_servico',
        'servico_mod_imp_id',
        'user_id',
        'status_anterior',
        'status_novo',
        'motivo',
    ];

    public function servico(): BelongsTo
    {
        return $this->belongsTo(Servicos::class, 'servico_id');
    }

    public function contrato(): BelongsTo
    {
        return $this->belongsTo(Contrato::class, 'contrato_id');
    }

    public function tema(): BelongsTo
    {
        return $this->belongsTo(ServicoTema::class, 'tema_servico');
    }

    public function moduloImportado(): BelongsTo
    {
        return $this->belongsTo(Modulo::class, 'servico_mod_imp_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
