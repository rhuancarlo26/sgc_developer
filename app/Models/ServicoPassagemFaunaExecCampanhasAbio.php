<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoPassagemFaunaExecCampanhasAbio extends Model
{
    use HasFactory;

    protected $table = 'passagem_fauna_exec_campanhas_abio';
    protected $guarded = ['id'];

    public function abio()
    {
        return $this->belongsTo(ServicoPassagemFaunaConfigAbio::class, 'id_abio');
    }
}
