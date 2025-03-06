<?php

namespace App\Models;

use App\Domain\Contrato\GestaoContrato\Trait\GeometryFromGeoJson;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class contratoTrecho extends Model
{
    use HasFactory, GeometryFromGeoJson;

    protected $table = 'trecho_contrato';

    protected $guarded = ['id', 'created_at'];

    protected $hidden = ['geometria'];

    protected static string $geoJSONColumn = 'coordenada';
    protected static string $geometryColumn = 'geometria';

    public function uf(): BelongsTo
    {
        return $this->belongsTo(Uf::class, 'uf_id');
    }

    public function rodovia(): BelongsTo
    {
        return $this->belongsTo(Rodovia::class, 'rodovia_id');
    }
}