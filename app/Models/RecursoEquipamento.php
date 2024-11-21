<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecursoEquipamento extends Model
{
    use HasFactory;

    protected $table = 'equipamentos';
    protected $guarded = ['id', 'created_at'];

    protected $casts = [
        'alugado' => 'bool'
    ];

    public function documentos()
    {
        return $this->hasMany(RecursoEquipamentoDocumento::class, 'cod_equipamento');
    }
}
