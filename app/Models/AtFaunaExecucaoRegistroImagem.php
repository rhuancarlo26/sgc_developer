<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AtFaunaExecucaoRegistroImagem extends Model
{
    use SoftDeletes;

    protected $table = 'at_fauna_execucao_registro_imagem';

    protected $guarded = ['id'];
}
