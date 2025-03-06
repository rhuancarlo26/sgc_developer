<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoPassagemFaunaExecCampanhasRh extends Model
{
    use HasFactory;

    protected $table = 'passagem_fauna_exec_campanhas_rh';
    protected $guarded = ['id'];

    public function servico_rh()
    {
        return $this->belongsTo(ServicoRh::class, 'id_servico_rh');
    }
}
