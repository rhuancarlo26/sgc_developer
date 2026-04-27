<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModuloImportadorHistorico extends Model
{
    use HasFactory;

    protected $table = 'modulo_importador_historicos';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'created_at' => 'datetime:d/m/Y',
    ];

    public function statusHistoricoFormatado(): Attribute
    {
        return new Attribute(
            get: function ($value, $attributes) {
                return match ((int) $this->status) {
                    1 => 'Rascunho',
                    2 => 'Aguardando Análise',
                    3 => 'Reprovado',
                    4 => 'Aprovado',
                    default => '-'
                };
            }
        );
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
