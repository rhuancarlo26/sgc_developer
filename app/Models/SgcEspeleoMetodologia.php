<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SgcEspeleoMetodologia extends Model
{
    use HasFactory;

    protected $table = 'sgc_espeleo_metodologias';

    protected $fillable = [
        'campanha_id',
        'id_contrato',
        'titulo',
        'metodologia',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relação com a campanha de Espeleologia.
     */
    public function campanha()
    {
        return $this->belongsTo(SgcEspeleoCampanha::class, 'campanha_id');
    }

    /**
     * Relação com o contrato (denormalizada).
     */
    public function contrato()
    {
        return $this->belongsTo(Contrato::class, 'id_contrato');
    }
}