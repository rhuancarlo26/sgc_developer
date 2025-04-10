<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AtFaunaExecucaoCampanhaEquipe extends Model
{
  use SoftDeletes;

  protected $table = 'at_fauna_execucao_campanha_equipes';

  protected $guarded = ['id'];

  public function servico_rh()
  {
    return $this->belongsTo(ServicoRh::class, 'servico_rh_id');
  }
}
