<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SgcMalarigenoAnexo extends Model
{
    protected $table = 'sgc_malarigeno_anexos';

    protected $fillable = [
        'sgc_malarigeno_id',
        'nome_arquivo',
        'caminho_arquivo',
    ];

    public function malarigeno()
    {
        return $this->belongsTo(SgcMalarigeno::class, 'sgc_malarigeno_id');
    }
}
