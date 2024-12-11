<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoMonitoraFaunaExecRegistro extends Model
{
    use HasFactory;

    protected $table = 'fauna_exec_registros';
    protected $guarded = ['id'];
}
