<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ServicoPassagemFaunaExecRegistro extends Model
{
    use HasFactory;

    protected $table = 'passagem_fauna_exec_registros';
    protected $guarded = ['id'];

    public function passagem(): BelongsTo
    {
        return $this->belongsTo(ServicoPassagemFaunaConfigPassagem::class, 'id_passagem');
    }

    public function campanha(): BelongsTo
    {
        return $this->belongsTo(ServicoPassagemFaunaExecCampanha::class, 'id_campanha');
    }

    public function imagem(): HasOne
    {
        return $this->hasOne(ServicoPassagemFaunaExecRegistroImagem::class, 'id_registro');
    }

    public function status_federal(): BelongsTo
    {
        return $this->belongsTo(ServicoPassagemFaunaExecStatusConservacao::class, 'id_status_conservacao_federal');
    }

    public function status_iunc(): BelongsTo
    {
        return $this->belongsTo(ServicoPassagemFaunaExecStatusConservacao::class, 'id_status_conservacao_iucn');
    }

    public function grupo_faunistico()
    {
        return $this->belongsTo(ServicoMonitoraFaunaExecGrupoFaunistico::class, 'id_grupo');
    }
}
