<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SgcEspeleoResultadoAnexo extends Model
{
    use HasFactory;

    protected $table = 'sgc_espeleo_resultados_anexos';

    protected $fillable = [
        'campanha_id',
        'id_contrato',
        'nome_arquivo',
        'caminho',
        'tipo',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function campanha()
    {
        return $this->belongsTo(SgcEspeleoCampanha::class, 'campanha_id');
    }

    public function contrato()
    {
        return $this->belongsTo(Contrato::class, 'id_contrato');
    }
}