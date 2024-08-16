<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoConOcorrSupervisaoResultado extends Model
{
    use HasFactory;

    protected $table = 'supervisao_resultado';
    protected $guarded = ['id'];
}
