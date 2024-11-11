<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AtFaunaExecucaoCampanhaEquipamento extends Model
{
  use SoftDeletes;

  protected $table = 'at_fauna_execucao_campanha_equipamentos';

  protected $guarded = ['id'];

  public function servico_equipamento()
  {
    return $this->belongsTo(ServicoEquipamento::class, 'servico_equipamento_id');
  }
}
