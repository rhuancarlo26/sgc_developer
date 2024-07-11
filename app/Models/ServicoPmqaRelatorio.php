<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoPmqaRelatorio extends Model
{
    use HasFactory;

    protected $table = 'servico_pmqa_relatorios';
    protected $guarded = ['id', 'created_at'];
}