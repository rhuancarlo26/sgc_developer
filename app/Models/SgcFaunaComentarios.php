<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SgcFaunaComentarios extends Model
{
    protected $table = 'sgc_fauna_comentarios';
    protected $guarded = ['id', 'created_at'];

    public function campanha()
    {
        return $this->belongsTo(SgcFaunaCampanha::class, 'campanha_id', 'id');
    }

}