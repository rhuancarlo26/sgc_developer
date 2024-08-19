<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AfugentFaunaExecFrenteModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'afugent_fauna_exec_frente';

    protected $guarded = ['id', 'created_at'];

    public function rodovia()
    {
        return $this->belongsTo(Rodovia::class, 'rodovia');
    }

    public function ufInicial()
    {
        return $this->belongsTo(UF::class, 'uf_inicial');
    }

    public function ufFinal()
    {
        return $this->belongsTo(UF::class, 'uf_final');
    }
}
