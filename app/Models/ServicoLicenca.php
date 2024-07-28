<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicoLicenca extends Model
{

    protected $table = 'servico_licenca';

    protected $guarded = ['id', 'created_at'];
}
