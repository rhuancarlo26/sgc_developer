<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LicencaSegmento extends Model
{
  use HasFactory;

  protected $table = 'licenca_segmento';
  protected $guarded = ['id', 'created_at'];

  public function uf_inicial(): BelongsTo
  {
    return $this->belongsTo(Uf::class, 'uf_inicial_id');
  }

  public function uf_final(): BelongsTo
  {
    return $this->belongsTo(Uf::class, 'uf_final_id');
  }

  public function rodovia(): BelongsTo
  {
    return $this->belongsTo(Rodovia::class, 'rodovia_id');
  }
}
