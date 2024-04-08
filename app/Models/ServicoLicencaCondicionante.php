<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoLicencaCondicionante extends Model
{
    use HasFactory;

    protected $table = 'servico_licenca_condicionantes';
    protected $guarded = ['id', 'created_at'];
}