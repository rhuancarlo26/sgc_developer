<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecursoEquipamento extends Model
{
    use HasFactory;

    protected $table = 'recurso_equipamentos';
    protected $guarded = ['id', 'created_at'];

    public function documentos()
    {
        return $this->hasMany(RecursoEquipamentoDocumento::class, 'equipamento_id');
    }
}
