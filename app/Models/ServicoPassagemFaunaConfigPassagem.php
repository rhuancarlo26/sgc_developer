<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoPassagemFaunaConfigPassagem extends Model
{
    use HasFactory;

    protected $table = 'passagem_fauna_config_passagens';
    protected $guarded = ['id'];
}
