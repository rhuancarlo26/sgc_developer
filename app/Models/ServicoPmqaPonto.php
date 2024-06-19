<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoPmqaPonto extends Model
{
    use HasFactory;

    protected $table = 'servico_pmqa_pontos';
    protected $guarded = ['id', 'created_at'];
}
