<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class AtFaunaExecucaoCampanhaRet extends Model
{
    use SoftDeletes;

    protected $table = 'at_fauna_execucao_campanha_ret';

    protected $guarded = ['id'];
}
