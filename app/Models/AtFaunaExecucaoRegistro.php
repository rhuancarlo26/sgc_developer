<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AtFaunaExecucaoRegistro extends Model
{
    use SoftDeletes;

    protected $table = 'at_fauna_execucao_registro';

    protected $guarded = ['id'];

    public function grupo_faunistico()
    {
        return $this->belongsTo(AtFaunaGrupoAmostradoModel::class, 'fk_grupo_amostrado');
    }

    public function campanhas()
    {
        return $this->belongsTo(AtFaunaExecucaoCampanha::class, 'fk_campanha');
    }

    protected static function booted(): void
    {
        static::created(function (AtFaunaExecucaoRegistro $registro) {
            $registro->nome_registro = 'AT.';

            switch ($registro->fk_grupo_amostrado) {
                case 1:
                    $registro->nome_registro .= 'A';
                    break;
                case 2:
                    $registro->nome_registro .= 'H';
                    break;
                case 3:
                    $registro->nome_registro .= 'M';
                    break;
            }

            $registro->nome_registro .= match (strlen($registro->id)) {
                1 => '00' . $registro->id,
                2 => '0' . $registro->id,
                default => $registro->id,
            };

            $registro->save();
        });
    }
}
