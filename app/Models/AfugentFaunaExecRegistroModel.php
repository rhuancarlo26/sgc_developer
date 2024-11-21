<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AfugentFaunaExecRegistroModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'afugent_fauna_exec_registro';

    protected $guarded = ['id', 'created_at'];

    public function formaRegistro()
    {
        return $this->belongsTo(AfugentFaunaFormaRegistroModel::class, 'id_forma_registro');
    }

    public function tipoRegistro()
    {
        return $this->belongsTo(AfugentFaunaTipoRegistroModel::class, 'id_tipo_registro');
    }

    public function destinacaoRegistro()
    {
        return $this->belongsTo(AfugentFaunaDestinacaoRegistroModel::class, 'id_destinacao_registro');
    }
}
