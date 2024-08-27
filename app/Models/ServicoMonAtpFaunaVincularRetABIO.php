<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServicoMonAtpFaunaVincularRetABIO extends Model
{
    use SoftDeletes;

    protected $table = 'at_fauna_config_vinculacao_ret';
    protected $guarded = ['id'];

}
