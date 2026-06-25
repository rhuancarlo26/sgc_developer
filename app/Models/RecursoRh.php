<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecursoRh extends Model
{
    use HasFactory;

    protected $table = 'rh';
    protected $guarded = ['id', 'created_at'];

    protected $casts = [
        'status' => 'boolean',
        'conselho_classe' => 'boolean',
    ];

    public function documentos()
    {
        return $this->hasMany(RecursoRhDocumento::class, 'cod_rh');
    }

//    public function documentosBaixa()
//    {
//        return $this->hasMany(RecursoRhDocumentoBaixa::class, 'recurso_rh_id');
//    }
}
