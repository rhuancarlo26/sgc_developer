<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModuloImportador extends Model
{
    use HasFactory;

    protected $table = 'modulo_importadores';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'load' => 'bool',
        'desc_erros' => 'array'
    ];

    public const RASCUNHO = 1;
    public const ANALISE = 2;
    public const REPROVADO = 3;
    public const APROVADO = 4;

    public function statusFormatado(): Attribute
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

    public function revisao(): Attribute
    {
        return new Attribute(
            get: function ($value, $attributes) {
                $revisao = $this->historicos->whereIn('status', [self::RASCUNHO, self::REPROVADO]);
                return $revisao->count();
            }
        );
    }

    public function modulo(): BelongsTo
    {
        return $this->belongsTo(Modulo::class, 'modulo_id');
    }

    public function dadosJson(): HasMany
    {
        return $this->hasMany(ModuloImportadorDados::class, 'modulo_importador_id');
    }

    public function fotos(): HasMany
    {
        return $this->hasMany(ModuloImportadorFotos::class, 'modulo_importador_id');
    }

    public function anexos(): HasMany
    {
        return $this->hasMany(ModuloImportadorAnexos::class, 'modulo_importador_id');
    }

    public function historicos(): HasMany
    {
        return $this->hasMany(ModuloImportadorHistorico::class, 'modulo_importador_id');
    }

    public function contrato()
    {
        return $this->belongsTo(Contrato::class, 'contrato_id');
    }

    public function servico()
    {
        return $this->belongsTo(Servicos::class, 'servico_id');
    }
}
