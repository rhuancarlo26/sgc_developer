<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoPmqaConfiguracaoParecer extends Model
{
    use HasFactory;

    protected $table = 'servico_pmqa_configuracao_parecer';
    protected $guarded = ['id', 'created_at'];
}
