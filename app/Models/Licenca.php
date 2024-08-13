<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Licenca extends Model
{
    use HasFactory;

    protected $table = 'licencas';
    protected $guarded = ['id', 'created_at'];
    protected $appends = ['iniciais', 'finais', 'brs'];

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(LicencaTipo::class, 'tipo');
    }

    public function condicionantes(): HasMany
    {
        return $this->hasMany(LicencaCondicionante::class, 'licencas_id');
    }

    public function segmentos(): HasMany
    {
        return $this->hasMany(LicencaSegmento::class, 'licenca_id');
    }

    public function documento(): HasOne
    {
        return $this->hasOne(LicencaDocumento::class, 'licenca_id');
    }

    public function shapefile(): HasOne
    {
        return $this->hasOne(LicencaShapefile::class, 'licenca_id');
    }

    public function requerimentos(): HasMany
    {
        return $this->hasMany(LicencaRequerimento::class, 'id_licenca');
    }

    public function iniciais(): Attribute
    {
        return Attribute::make(
            get: function () {
                $ufs = [];

                foreach ($this->segmentos as $value) {
                    $uf = $value->uf_inicial->uf;

                    $uf ? array_push($ufs, trim($uf)) : '';
                }

                return implode(",", array_unique($ufs));
            }
        );
    }

    public function finais(): Attribute
    {
        return Attribute::make(
            get: function () {
                $ufs = [];

                foreach ($this->segmentos as $value) {
                    $uf = $value->uf_final->uf;

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

                foreach ($this->segmentos as $value) {
                    $rodovia = $value->rodovia;

                    $rodovia ? array_push($rodovias, trim($rodovia)) : '';
                }

                return implode(",", array_unique($rodovias));
            }
        );
    }

    public function licencaServicos(): HasMany
    {
        return $this->hasMany(ServicoLicenca::class, 'licenca_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
