<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LicencaSegmento extends Model
{
  use HasFactory;

  protected $table = 'licencas_br';
  protected $guarded = ['id', 'created_at'];

  public function uf_inicial(): BelongsTo
  {
    return $this->belongsTo(Uf::class, 'uf_inicial');
  }

  public function uf_final(): BelongsTo
  {
    return $this->belongsTo(Uf::class, 'uf_final');
  }
}
