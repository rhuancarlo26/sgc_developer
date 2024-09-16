<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoPassagemFaunaExecCampanhasRet extends Model
{
    use HasFactory;

    protected $table = 'passagem_fauna_exec_campanhas_ret';
    protected $guarded = ['id'];

    public function ret()
    {
        return $this->belongsTo(ServicoPassagemFaunaConfigAbioRet::class, 'id_ret');
    }
}
