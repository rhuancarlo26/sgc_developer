<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AtFaunaExecucaoCampanhaVeiculo extends Model
{
  use SoftDeletes;

  protected $table = 'at_fauna_execucao_campanha_veiculos';

  protected $guarded = ['id'];

  public function servico_veiculo()
  {
    return $this->belongsTo(ServicoVeiculo::class, 'servico_veiculo_id');
  }
}
