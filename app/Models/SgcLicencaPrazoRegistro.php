<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SgcLicencaPrazoRegistro extends Model
{
    protected $table = 'sgc_licencas_prazo_registros';

    protected $fillable = [
        'prazo_id', 'tipo', 'data', 'numero_oficio', 'numero_sei', 'descricao', 'data_limite_anterior',
        'data_limite_nova', 'arquivo_nome', 'arquivo_caminho', 'user_id',
    ];

    protected $casts = [
        'data' => 'date:Y-m-d',
        'data_limite_anterior' => 'date:Y-m-d',
        'data_limite_nova' => 'date:Y-m-d',
    ];

    protected $hidden = ['arquivo_caminho'];

    public function prazo(): BelongsTo
    {
        return $this->belongsTo(SgcLicencaPrazo::class, 'prazo_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
