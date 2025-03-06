<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rodovia extends Model
{
    use HasFactory;

    protected $table = 'base_rodovia';

    public function uf()
    {
        return $this->belongsTo(Uf::class, 'estados_id');
    }
}
