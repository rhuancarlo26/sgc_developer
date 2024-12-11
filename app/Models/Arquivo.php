<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Arquivo extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id', 'created_at'];

    protected $appends = ['caminho'];

    public function caminho(): Attribute
    {
        return Attribute::make(
            get: fn () => asset('storage/' . $this->diretorio . $this->arquivo)
        );
    }
}
