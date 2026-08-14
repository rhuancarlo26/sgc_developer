<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SgcPatrimonioShapefile extends Model
{
  protected $table = 'sgc_patrimonio_shapefile';
  protected $fillable = [
    'patrimonio_paipa_id',
    'nome_campo',
    'geo_json',
    'workspace',
    'datastore',
    'layer_name',
    'storage_path',
    'published_at',
  ];

  protected $casts = [
    'geo_json' => 'array',
    'published_at' => 'datetime',
  ];

  public function paipa(): BelongsTo
  {
    return $this->belongsTo(SgcPatrimonioPaipa::class, 'patrimonio_paipa_id');
  }
}
