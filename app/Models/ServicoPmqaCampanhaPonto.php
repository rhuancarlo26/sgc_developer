<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoPmqaCampanhaPonto extends Model
{
    use HasFactory;

    protected $table = 'servico_pmqa_campanha_pontos';
    protected $guarded = ['id', 'created_at'];
}