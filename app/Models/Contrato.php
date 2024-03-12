<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Contrato extends Model
{
    use HasFactory;

    protected $table = 'contratos';

    protected $guarded = ['id', 'created_at'];

    protected $appends = ['ufs', 'brs'];

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(ContratoTipo::class, 'tipo_id');
    }

    public function trechos(): HasMany
    {
        return $this->hasMany(contratoTrecho::class, 'contrato_id');
    }

    public function introducao(): HasOne
    {
        return $this->hasOne(ContratoIntroducao::class, 'contrato_id');
    }

    public function licenciamentos()
    {
        return $this->hasManyThrough(Licenca::class, ContratoLicenca::class, 'contrato_id', 'id', 'id', 'licenca_id');
    }

    public function licenciamento_observacoes()
    {
        return $this->hasMany(ContratoLicencaObservacao::class, 'contrato_id');
    }

    public function ufs(): Attribute
    {
        return Attribute::make(
            get: function () {
                $ufs = [];

                foreach ($this->trechos as $value) {
                    $uf = $value->uf->uf;

                    $uf ? array_push($ufs, trim($uf)) : '';
                }

                return implode(",", array_unique($ufs));
            }
        );
    }

    public function brs(): Attribute
    {
        return Attribute::make(
            get: function () {
                $rodovias = [];

                foreach ($this->trechos as $value) {
                    $rodovia = $value->rodovia->rodovia;

                    $rodovia ? array_push($rodovias, trim($rodovia)) : '';
                }

                return implode(",", array_unique($rodovias));
            }
        );
    }
}