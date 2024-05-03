<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LicencaCondicionante extends Model
{
  use HasFactory;

  protected $table = 'licenca_condicionantes';
  protected $guarded = ['id', 'created_at'];

  public function licenca(): BelongsTo
  {
    return $this->belongsTo(Licenca::class, 'licenca_id');
  }
}