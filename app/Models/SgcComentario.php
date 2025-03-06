<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SgcComentario extends Model
{
    use HasFactory;

    protected $table = 'sgc_comentario';
    protected $guarded = ['id', 'created_at'];

    public function comment(): HasMany
    {
        return $this->hasMany(SgcComentarios::class, 'comentario_id', 'id');
    }
}
