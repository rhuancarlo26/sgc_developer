<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupressaoRelatorioAnexo extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $appends = ['caminho'];

    public function caminho(): Attribute
    {
        return Attribute::make(
            get: fn () => asset('storage/' . $this->caminho_arquivo)
        );
    }
}
