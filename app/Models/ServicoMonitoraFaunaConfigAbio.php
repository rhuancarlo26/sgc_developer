<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoMonitoraFaunaConfigAbio extends Model
{
    use HasFactory;

    protected $table = 'fauna_config_abio';
    protected $guarded = ['id'];
}
