<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoConOcorrSupervisaoRelatorio extends Model
{
    use HasFactory;

    protected $table = 'supervisao_relatorio';
    protected $guarded = ['id'];
}
