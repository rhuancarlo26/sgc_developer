<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ServicoPassagemFaunaResultado extends Model
{
    use HasFactory;

    protected $table = 'passagem_fauna_resultado';
    protected $guarded = ['id'];

    public function campanhas(): BelongsToMany
    {
        return $this->belongsToMany(ServicoPassagemFaunaExecCampanha::class, ServicoPassagemFaunaResultadoCampanha::class, 'id_resultado', 'id_campanha');
    }

    public function analise()
    {
        return $this->hasOne(ServicoPassagemFaunaResultadoAnalise::class, 'id_resultado');
    }

    public function outras_analises()
    {
        return $this->hasMany(ServicoPassagemFaunaResultadoOutrasAnalise::class, 'id_resultado');
    }
}
