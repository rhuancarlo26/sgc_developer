<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoMonitoraFaunaExecStatusConservacao extends Model
{
    use HasFactory;

    protected $table = 'fauna_exec_status_conservacao';
    protected $guarded = ['id'];
}
