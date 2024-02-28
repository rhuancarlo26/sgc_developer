<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rodovia extends Model
{
    use HasFactory;

    protected $table = 'rodovias';

    public function uf()
    {
        return $this->belongsTo(Uf::class, 'uf_id');
    }
}